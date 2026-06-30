<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the monitor dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // 1. Fetch Queue & Delivery Statistics
        $statsQuery = Message::query();
        if (!$isAdmin) {
            $statsQuery->where('user_id', $user->id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'sent' => (clone $statsQuery)->where('status', 'sent')->count(),
            'delivered' => (clone $statsQuery)->where('status', 'delivered')->count(),
            'read' => (clone $statsQuery)->where('status', 'read')->count(),
            'failed' => (clone $statsQuery)->where('status', 'failed')->count(),
            'received' => (clone $statsQuery)->where('status', 'received')->count(),
        ];

        // 2. Fetch Print Monitor Folder Statistics (Admin only feature usually, or restricted)
        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $folderExists = File::exists($folderPath);
        
        $folderStats = [
            'path' => $folderPath,
            'exists' => $folderExists,
            'is_writable' => $folderExists && File::isWritable($folderPath),
            'pending_files' => 0,
            'archived_files' => 0,
            'failed_files' => 0,
            'files_list' => [],
        ];

        if ($folderExists && $isAdmin) {
            // Count pending files (directly in folder, ignore subdirectories)
            $allFiles = File::files($folderPath);
            $pendingFilesCount = 0;
            $recentFiles = [];
            
            foreach ($allFiles as $file) {
                if (!str_starts_with($file->getFilename(), '.')) {
                    $pendingFilesCount++;
                    if (count($recentFiles) < 5) {
                        $recentFiles[] = [
                            'name' => $file->getFilename(),
                            'size' => round($file->getSize() / 1024, 2) . ' KB',
                            'time' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                    }
                }
            }
            
            $folderStats['pending_files'] = $pendingFilesCount;
            $folderStats['files_list'] = $recentFiles;

            // Count archived files
            $archivePath = $folderPath . '/archive';
            if (File::exists($archivePath)) {
                $folderStats['archived_files'] = count(File::files($archivePath));
            }

            // Count failed files
            $failedPath = $folderPath . '/failed';
            if (File::exists($failedPath)) {
                $folderStats['failed_files'] = count(File::files($failedPath));
            }
        }

        // 3. Fetch Recent Messages
        $messagesQuery = Message::query();
        if (!$isAdmin) {
            $messagesQuery->where('user_id', $user->id);
        }
        $recentMessages = $messagesQuery->latest()->take(10)->get();

        // 4. Fetch Hourly/Daily Chart Data (Last 7 Days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData['labels'][] = now()->subDays($i)->translatedFormat('l');
            
            $sentQuery = Message::whereDate('created_at', $date)
                ->whereIn('status', ['sent', 'delivered', 'read', 'received']);
            
            $failedQuery = Message::whereDate('created_at', $date)
                ->where('status', 'failed');

            if (!$isAdmin) {
                $sentQuery->where('user_id', $user->id);
                $failedQuery->where('user_id', $user->id);
            }

            $chartData['sent'][] = $sentQuery->count();
            $chartData['failed'][] = $failedQuery->count();
        }

        // 5. Check Server Connection Status
        $serverStatus = [
            'connected' => false,
            'message' => 'جاري الفحص...',
            'url' => config('app.central_api_url')
        ];
        
        try {
            if (!empty($serverStatus['url'])) {
                // We send a lightweight request. Even if 404, it means the server is reachable.
                $http = \Illuminate\Support\Facades\Http::timeout(5);
                
                if (env('API_VERIFY_SSL', true) === false || env('API_VERIFY_SSL') === 'false') {
                    $http->withoutVerifying();
                }
                
                $response = $http->get($serverStatus['url']);
                $serverStatus['connected'] = true;
                $serverStatus['message'] = 'متصل بالسيرفر بنجاح';
            } else {
                $serverStatus['message'] = 'لم يتم تعيين رابط السيرفر (CENTRAL_API_URL)';
            }
        } catch (\Exception $e) {
            $serverStatus['connected'] = false;
            if ($e instanceof \Illuminate\Http\Client\ConnectionException || str_contains($e->getMessage(), 'cURL error')) {
                $serverStatus['message'] = 'لا يمكن الوصول للسيرفر (تأكد من العنوان أو أن السيرفر يعمل)';
            } else {
                $serverStatus['message'] = 'خطأ في الاتصال: ' . $e->getMessage();
            }
        }

        return view('dashboard', compact('stats', 'folderStats', 'recentMessages', 'chartData', 'serverStatus'));
    }

    /**
     * Trigger Print Monitor folder scan.
     */
    public function scanFolder()
    {
        try {
            $exitCode = Artisan::call('monitor:folder');
            $output = Artisan::output();
            
            Log::info('Dashboard Manual Folder Scan triggered', [
                'exit_code' => $exitCode,
                'output' => $output
            ]);

            return redirect()->route('dashboard')->with('success', 'تم فحص المجلد بنجاح: ' . nl2br($output));
        } catch (\Exception $e) {
            Log::error('Dashboard Manual Folder Scan failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل فحص المجلد: ' . $e->getMessage());
        }
    }

    /**
     * Retry all failed messages.
     */
    public function retryAllFailed()
    {
        try {
            $failedMessages = Message::where('status', 'failed')->get();
            
            if ($failedMessages->isEmpty()) {
                return redirect()->route('dashboard')->with('info', 'لا توجد رسائل فاشلة لإعادة إرسالها');
            }

            foreach ($failedMessages as $message) {
                $message->update([
                    'status' => 'pending',
                    'error_message' => null,
                    'retry_count' => $message->retry_count + 1
                ]);
                
                dispatch(new SendMessageJob($message->id));
            }

            return redirect()->route('dashboard')->with('success', 'تم إعادة جدولة ' . $failedMessages->count() . ' رسائل فاشلة بنجاح');
        } catch (\Exception $e) {
            Log::error('Dashboard Retry All Failed failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل إعادة إرسال الرسائل الفاشلة: ' . $e->getMessage());
        }
    }

    /**
     * Force process active queue worker once.
     */
    public function processQueue()
    {
        try {
            $exitCode = Artisan::call('queue:work', ['--once' => true]);
            $output = Artisan::output();
            
            return redirect()->route('dashboard')->with('success', 'تم معالجة قائمة الانتظار بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'فشل معالجة قائمة الانتظار: ' . $e->getMessage());
        }
    }
    /**
     * Start background services (queue & schedule).
     */
    public function startServices()
    {
        try {
            // التحقق مما إذا كانت الخدمات تعمل مسبقاً
            exec('wmic process where "name=\'php.exe\' and commandline like \'%artisan queue:work%\'" get processid 2>NUL', $outputQueue);
            exec('wmic process where "name=\'php.exe\' and commandline like \'%artisan schedule:work%\'" get processid 2>NUL', $outputSchedule);
            
            $queueRunning = false;
            foreach($outputQueue as $line) {
                if (is_numeric(trim($line))) $queueRunning = true;
            }
            
            $scheduleRunning = false;
            foreach($outputSchedule as $line) {
                if (is_numeric(trim($line))) $scheduleRunning = true;
            }

            $basePath = base_path();
            $phpPath = 'c:\xampp\php\php.exe';
            
            $messages = [];

            if (!$queueRunning) {
                $cmdQueue = "powershell -windowstyle hidden -command \"Start-Process '$phpPath' -ArgumentList 'artisan queue:work' -WorkingDirectory '$basePath' -WindowStyle Hidden\"";
                pclose(popen("start /B " . $cmdQueue, "r"));
                $messages[] = 'تم تشغيل عامل الطابور (Queue Worker).';
            } else {
                $messages[] = 'عامل الطابور يعمل مسبقاً.';
            }

            if (!$scheduleRunning) {
                $cmdSchedule = "powershell -windowstyle hidden -command \"Start-Process '$phpPath' -ArgumentList 'artisan schedule:work' -WorkingDirectory '$basePath' -WindowStyle Hidden\"";
                pclose(popen("start /B " . $cmdSchedule, "r"));
                $messages[] = 'تم تشغيل المجدول (Scheduler).';
            } else {
                $messages[] = 'المجدول يعمل مسبقاً.';
            }

            return redirect()->route('dashboard')->with('success', implode(' ', $messages));
        } catch (\Exception $e) {
            Log::error('Dashboard Start Services failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل تشغيل الخدمات: ' . $e->getMessage());
        }
    }

    /**
     * Restart Queue workers.
     */
    public function restartQueue()
    {
        try {
            // Tell existing workers to stop gracefully (they will stop after finishing current job)
            Artisan::call('queue:restart');
            
            // Also forcefully kill the queue worker process to restart it immediately since we run it in a hidden window without a daemon manager
            exec('wmic process where "name=\'php.exe\' and commandline like \'%artisan queue:work%\'" call terminate 2>NUL');
            
            // Wait a moment for processes to terminate
            sleep(1);
            
            // Start the queue worker again
            $basePath = base_path();
            $phpPath = 'c:\xampp\php\php.exe';
            
            $cmdQueue = "powershell -windowstyle hidden -command \"Start-Process '$phpPath' -ArgumentList 'artisan queue:work' -WorkingDirectory '$basePath' -WindowStyle Hidden\"";
            pclose(popen("start /B " . $cmdQueue, "r"));
            
            return redirect()->route('dashboard')->with('success', 'تم إعادة تشغيل الطابور بنجاح.');
        } catch (\Exception $e) {
            Log::error('Dashboard Restart Queue failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل إعادة تشغيل الطابور: ' . $e->getMessage());
        }
    }
}
