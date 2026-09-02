<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\PrintJob;
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
        if (!$isAdmin && !$user->isSupervisor()) {
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
            $folderStats['archived_files_list'] = [];
            if (File::exists($archivePath)) {
                $archivedFiles = File::files($archivePath);
                $folderStats['archived_files'] = count($archivedFiles);
                $recentArchived = array_slice($archivedFiles, -50); // Get last 50 for performance
                foreach ($recentArchived as $file) {
                    if (!str_starts_with($file->getFilename(), '.')) {
                        $trace = \App\Models\ExtractionTrace::where('filename', $file->getFilename())->latest()->first();
                        $folderStats['archived_files_list'][] = [
                            'name' => $file->getFilename(),
                            'size' => round($file->getSize() / 1024, 2) . ' KB',
                            'time' => date('Y-m-d H:i:s', $file->getMTime()),
                            'trace' => $trace
                        ];
                    }
                }
            }

            // Count failed files
            $failedPath = $folderPath . '/failed';
            $folderStats['failed_files_list'] = [];
            if (File::exists($failedPath)) {
                $failedFiles = File::files($failedPath);
                $folderStats['failed_files'] = count($failedFiles);
                foreach ($failedFiles as $file) {
                    if (!str_starts_with($file->getFilename(), '.')) {
                        $trace = \App\Models\ExtractionTrace::where('filename', $file->getFilename())->latest()->first();
                        $folderStats['failed_files_list'][] = [
                            'name' => $file->getFilename(),
                            'size' => round($file->getSize() / 1024, 2) . ' KB',
                            'time' => date('Y-m-d H:i:s', $file->getMTime()),
                            'trace' => $trace
                        ];
                    }
                }
            }
        }

        // 2.5 Fetch pending-approval counts (print jobs awaiting approval + monitor-folder files
        // awaiting manual review before sending) — تظهر كتنبيه بارز في اللوحة الرئيسية حتى لا ينسى
        // المسؤول طلباً معلّقاً لساعات لمجرد أنه لم يفتح صفحة الطباعة أو متابعة الإرسال بنفسه.
        $pendingApprovals = [
            'print_jobs' => PrintJob::where('status', 'awaiting_approval')->count(),
            'review_messages' => Message::where('status', 'review_pending')->count(),
        ];

        // 3. Fetch Recent Messages
        $messagesQuery = Message::query();
        if (!$isAdmin && !$user->isSupervisor()) {
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

            if (!$isAdmin && !$user->isSupervisor()) {
                $sentQuery->where('user_id', $user->id);
                $failedQuery->where('user_id', $user->id);
            }

            $chartData['sent'][] = $sentQuery->count();
            $chartData['failed'][] = $failedQuery->count();
        }

        // 5. Check Server Connection Status
        $serverStatus = [
            'connected' => false,
            'message' => __('local_agent.checking_connection'),
            'url' => config('app.central_api_url')
        ];

        if (!empty($serverStatus['url'])) {
            try {
                $centralApi = app(\App\Services\CentralApiService::class);
                $result = $centralApi->checkConnection();

                $serverStatus['connected'] = $result['success'];
                if ($result['success']) {
                    $serverStatus['message'] = __('local_agent.server_connected_ok');
                } else {
                    $serverStatus['message'] = __('local_agent.server_connected_with_error', ['detail' => $result['message'] ?? __('local_agent.check_token_and_company')]);
                }
            } catch (\Exception $e) {
                $serverStatus['connected'] = false;
                if ($e instanceof \Illuminate\Http\Client\ConnectionException || str_contains($e->getMessage(), 'cURL error')) {
                    $serverStatus['message'] = __('local_agent.server_unreachable');
                } else {
                    $serverStatus['message'] = __('local_agent.connection_error', ['detail' => $e->getMessage()]);
                }
            }
        } else {
            $serverStatus['message'] = __('local_agent.server_url_not_set');
        }

        // 5.5 فحص حالة اتصال واتساب الفعلية — منفصل عن فحص الوصول للسيرفر المركزي أعلاه، الذي
        // لا يعرف شيئاً عن حالة جلسة واتساب نفسها. يُنفَّذ دائماً بشكل مستقل (بدل الاعتماد على
        // نجاح فحص السيرفر أعلاه أولاً)، لأن ذلك كان يُخفي حالة واتساب الحقيقية بالكامل عند فشل
        // فحص السيرفر (مثلاً بسبب توكن غير صالح) فتظهر اللوحة "غير متصل" دون أي توضيح لسبب ذلك،
        // رغم أن واتساب قد يكون متصلاً فعلياً على الجهاز/المركزي. checkWhatsAppStatus() تتعامل مع
        // الأخطاء داخلياً وتعيد status='error' بدل رمي استثناء، لذا لا حاجة لبوابة مسبقة هنا.
        try {
            $centralApi = app(\App\Services\CentralApiService::class);
            $whatsappStatus = $centralApi->checkWhatsAppStatus();
        } catch (\Exception $e) {
            $whatsappStatus = [
                'connected' => false,
                'status' => 'error',
                'message' => 'خطأ أثناء فحص حالة واتساب: ' . $e->getMessage(),
                'provider' => null,
            ];
        }

        // حالة "غير مؤكدة" (unknown) منفصلة عن "غير متصل" المؤكدة: تُستخدم عندما فشل الفحص نفسه
        // (خطأ شبكة/مصادقة/استثناء) بدل أن يُبلغ المركزي فعلياً أن واتساب غير متصل. الواجهة تعرض
        // لوناً مختلفاً (كهرماني) لهذه الحالة بدل الأحمر المخصص للحالة المؤكدة.
        $whatsappStatus['check_failed'] = in_array($whatsappStatus['status'] ?? null, ['error', 'failed'], true);

        // 5.6 تنبيه "مانع" ظاهر في اللوحة — يُرفَع من SendMessageJob عند أخطاء دائمة (مصادقة/باقة) تمنع
        // إرسال أي رسالة، بدل أن يبقى الخطأ مدفوناً في ملف الـ log فقط.
        $centralBlockingError = null;
        $rawBlockingError = \App\Models\Setting::get('CENTRAL_BLOCKING_ERROR');
        if (!empty($rawBlockingError)) {
            $centralBlockingError = json_decode($rawBlockingError, true);
        }

        // 6. Check Background Services Status (via PID files owned by this application only)
        $queueRunning = $this->isTrackedProcessRunning('queue');
        $scheduleRunning = $this->isTrackedProcessRunning('schedule');

        $servicesStatus = [
            'queue' => $queueRunning,
            'schedule' => $scheduleRunning,
            'all_running' => $queueRunning && $scheduleRunning
        ];

        return view('dashboard', compact('stats', 'folderStats', 'recentMessages', 'chartData', 'serverStatus', 'whatsappStatus', 'servicesStatus', 'pendingApprovals', 'centralBlockingError'));
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

            return redirect()->route('dashboard')->with('success', 'تم فحص المجلد بنجاح: ' . $output);
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
     * Verify connection to the central server manually.
     */
    public function checkConnection()
    {
        try {
            $centralApi = app(\App\Services\CentralApiService::class);
            $result = $centralApi->checkConnection();
            
            if ($result['success']) {
                return redirect()->route('dashboard')->with('success', 'نجاح المصادقة: ' . $result['message']);
            } else {
                return redirect()->route('dashboard')->with('error', 'فشل المصادقة: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Dashboard Check Connection failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'خطأ في الاتصال بالسيرفر: ' . $e->getMessage());
        }
    }

    /**
     * Verify actual WhatsApp session status manually — منفصل عن checkConnection() أعلاه الذي
     * يتحقق فقط من الوصول للسيرفر المركزي، لا من حالة جلسة واتساب نفسها.
     */
    public function checkWhatsAppConnection()
    {
        try {
            $centralApi = app(\App\Services\CentralApiService::class);
            $result = $centralApi->checkWhatsAppStatus();

            if ($result['connected'] ?? false) {
                return redirect()->route('dashboard')->with('success', 'واتساب متصل: ' . ($result['message'] ?? ''));
            }

            if (in_array($result['status'] ?? null, ['error', 'failed'], true)) {
                return redirect()->route('dashboard')->with('error', 'تعذّر التحقق من حالة واتساب: ' . ($result['message'] ?? 'خطأ غير معروف'));
            }

            return redirect()->route('dashboard')->with('error', 'واتساب غير متصل: ' . ($result['message'] ?? ''));
        } catch (\Exception $e) {
            Log::error('Dashboard Check WhatsApp Connection failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'خطأ أثناء فحص حالة واتساب: ' . $e->getMessage());
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
        activity('services')->causedBy(auth()->user())->log('تشغيل الخدمات (Queue/Scheduler)');

        try {
            $basePath = base_path();
            $phpPath = 'c:\xampp\php\php.exe';

            $messages = [];

            if (!$this->isTrackedProcessRunning('queue')) {
                $this->launchTrackedProcess('queue', $phpPath, $basePath, 'artisan queue:work');
                $messages[] = 'تم تشغيل عامل الطابور (Queue Worker).';
            } else {
                $messages[] = 'عامل الطابور يعمل مسبقاً.';
            }

            if (!$this->isTrackedProcessRunning('schedule')) {
                $this->launchTrackedProcess('schedule', $phpPath, $basePath, 'artisan schedule:work');
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
     * Stop background services (queue & schedule).
     */
    public function stopServices()
    {
        activity('services')->causedBy(auth()->user())->log('إيقاف الخدمات (Queue/Scheduler) — تعطيل مؤقت لإرسال/استقبال الرسائل بالكامل للشركة');

        try {
            $this->killTrackedProcess('queue');
            $this->killTrackedProcess('schedule');

            return redirect()->route('dashboard')->with('success', 'تم إيقاف الخدمات بنجاح.');
        } catch (\Exception $e) {
            Log::error('Dashboard Stop Services failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل إيقاف الخدمات: ' . $e->getMessage());
        }
    }

    /**
     * Restart Queue workers.
     */
    public function restartQueue()
    {
        activity('services')->causedBy(auth()->user())->log('إعادة تشغيل عامل الطابور (Queue Worker)');

        try {
            // Tell existing workers to stop gracefully (they will stop after finishing current job)
            Artisan::call('queue:restart');

            // Also forcefully kill our own tracked queue worker process to restart it immediately
            // since we run it in a hidden window without a daemon manager
            $this->killTrackedProcess('queue');

            // Wait a moment for the process to terminate
            sleep(1);

            $this->launchTrackedProcess('queue', 'c:\xampp\php\php.exe', base_path(), 'artisan queue:work');

            return redirect()->route('dashboard')->with('success', 'تم إعادة تشغيل الطابور بنجاح.');
        } catch (\Exception $e) {
            Log::error('Dashboard Restart Queue failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'فشل إعادة تشغيل الطابور: ' . $e->getMessage());
        }
    }

    /**
     * مسار ملف PID الخاص بخدمة معينة تابعة لهذا التطبيق فقط (queue أو schedule)
     */
    private function pidFilePath(string $service): string
    {
        return storage_path("app/{$service}_worker.pid");
    }

    /**
     * التحقق من كون العملية المسجّلة في ملف الـ PID الخاص بهذا التطبيق لا تزال حية
     * (بدل البحث في كل عمليات النظام عبر wmic، مما قد يطال تطبيقات أخرى على نفس الجهاز)
     */
    private function isTrackedProcessRunning(string $service): bool
    {
        $pidFile = $this->pidFilePath($service);
        if (!File::exists($pidFile)) {
            return false;
        }

        $pid = trim(File::get($pidFile));
        if (!ctype_digit($pid)) {
            return false;
        }

        exec('tasklist /FI "PID eq ' . $pid . '" /NH 2>NUL', $output);
        foreach ($output as $line) {
            if (str_contains($line, $pid)) {
                return true;
            }
        }

        // العملية لم تعد موجودة، نظّف ملف الـ PID القديم
        File::delete($pidFile);
        return false;
    }

    /**
     * تشغيل عملية خلفية جديدة وتسجيل رقمها (PID) الخاص بهذا التطبيق فقط
     */
    private function launchTrackedProcess(string $service, string $phpPath, string $basePath, string $artisanCommand): void
    {
        $escapedPhpPath = str_replace("'", "''", $phpPath);
        $escapedBasePath = str_replace("'", "''", $basePath);
        $escapedArgs = str_replace("'", "''", $artisanCommand);

        $psCommand = "\$p = Start-Process -FilePath '{$escapedPhpPath}' -ArgumentList '{$escapedArgs}' -WorkingDirectory '{$escapedBasePath}' -WindowStyle Hidden -PassThru; Write-Output \$p.Id";
        $cmd = 'powershell -NoProfile -WindowStyle Hidden -Command "' . $psCommand . '"';

        exec($cmd, $output);
        $pid = trim($output[0] ?? '');

        if (ctype_digit($pid)) {
            File::put($this->pidFilePath($service), $pid);
        } else {
            Log::error("Failed to capture PID for {$service} worker", ['output' => $output]);
        }
    }

    /**
     * إيقاف عملية مُتتبَّعة تابعة لهذا التطبيق فقط عبر رقمها (PID) المسجل
     */
    private function killTrackedProcess(string $service): void
    {
        $pidFile = $this->pidFilePath($service);
        if (!File::exists($pidFile)) {
            return;
        }

        $pid = trim(File::get($pidFile));
        if (ctype_digit($pid)) {
            exec('taskkill /F /PID ' . $pid . ' 2>NUL');
        }

        File::delete($pidFile);
    }
}
