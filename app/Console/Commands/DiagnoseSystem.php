<?php
// ملف: app/Console/Commands/DiagnoseSystem.php (النظام المحلي)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Message;

class DiagnoseSystem extends Command
{
    protected $signature = 'diagnose:system {--test-message}';
    protected $description = 'Diagnose system connectivity and configuration';

    public function handle()
    {
        $this->info('🔍 Starting System Diagnosis...');
        $this->newLine();

        // 1. فحص التكوين الأساسي
        $this->checkBasicConfiguration();
        $this->newLine();

        // 2. فحص الاتصال بقاعدة البيانات
        $this->checkDatabaseConnection();
        $this->newLine();

        // 3. فحص الاتصال بالنظام المركزي
        $this->checkCentralSystemConnection();
        $this->newLine();

        // 4. فحص المصادقة
        $this->checkAuthentication();
        $this->newLine();

        // 5. اختبار إرسال رسالة (اختياري)
        if ($this->option('test-message')) {
            $this->testMessageSending();
            $this->newLine();
        }

        $this->info('✅ Diagnosis completed!');
    }

    private function checkBasicConfiguration()
    {
        $this->info('📋 Checking Basic Configuration...');

        $configs = [
            'CENTRAL_API_URL' => config('app.central_api_url'),
            'CENTRAL_API_TOKEN' => config('app.central_api_token'),
            'COMPANY_ID' => config('app.company_id'),
            'APP_ENV' => config('app.env'),
            'APP_DEBUG' => config('app.debug') ? 'true' : 'false',
            'QUEUE_CONNECTION' => config('queue.default'),
        ];

        foreach ($configs as $key => $value) {
            if (empty($value)) {
                $this->error("❌ {$key}: Not set");
            } else {
                $displayValue = $key === 'CENTRAL_API_TOKEN' ? substr($value, 0, 10) . '...' : $value;
                $this->line("✅ {$key}: {$displayValue}");
            }
        }
    }

    private function checkDatabaseConnection()
    {
        $this->info('🗄️ Checking Database Connection...');

        try {
            DB::connection()->getPdo();
            $this->line('✅ Database connection: OK');

            // فحص الجداول المطلوبة
            $tables = ['messages', 'jobs', 'failed_jobs'];
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->line("✅ Table '{$table}': Exists ({$count} records)");
                } else {
                    $this->error("❌ Table '{$table}': Missing");
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
        }
    }

    private function checkCentralSystemConnection()
    {
        $this->info('🌐 Checking Central System Connection...');

        $centralUrl = config('app.central_api_url');

        if (empty($centralUrl)) {
            $this->error('❌ Central API URL not configured');
            return;
        }

        try {
            // فحص health endpoint
            $healthUrl = str_replace('/api', '/api/health', $centralUrl);
            $response = Http::timeout(10)->get($healthUrl);

            if ($response->successful()) {
                $data = $response->json();
                $this->line("✅ Central system health: {$data['status']}");
                $this->line('   System: '.($data['system'] ?? 'Unknown'));
                $this->line('   Version: '. ($data['version'] ?? 'Unknown'));
            } else {
                $this->error("❌ Central system health check failed: HTTP {$response->status()}");
                $this->error("   Response: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Cannot connect to central system: ' . $e->getMessage());
        }
    }

    private function checkAuthentication()
    {
        $this->info('🔐 Checking Authentication...');

        $centralUrl = config('app.central_api_url');
        $token = config('app.central_api_token');
        $companyId = config('app.company_id');

        if (empty($centralUrl) || empty($token) || empty($companyId)) {
            $this->error('❌ Authentication configuration incomplete');
            return;
        }

        try {
            // اختبار المصادقة مع المسار الصحيح
            $authUrl = str_replace('/api', '/api/auth/check', $centralUrl);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Company-ID' => $companyId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(10)->get($authUrl);

            if ($response->successful()) {
                $data = $response->json();
                $this->line('✅ Authentication successful');
//                $this->line("   Company ID: {$data['company_id'] ?? 'Unknown'}");
                $this->line('   Company ID: ' . ($data['company_id'] ?? 'Unknown'));
                if (isset($data['company']['name'])) {
                    $this->line("   Company Name: {$data['company']['name']}");
                }
            } else {
                $this->error("❌ Authentication failed: HTTP {$response->status()}");
                $this->error("   Response: " . $response->body());

                // اختبار بديل بدون مصادقة
                $this->line("   Trying alternative test...");
                $testUrl = str_replace('/api', '/api/auth-test', $centralUrl);
                $testResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Company-ID' => $companyId,
                ])->timeout(10)->get($testUrl);

                if ($testResponse->successful()) {
                    $testData = $testResponse->json();
                    $this->line("   ✅ Basic connectivity: OK");
                    $this->line("   Company ID received: {$testData['company_id']}");
                    $this->line("   Token received: " . ($testData['has_token'] ? 'Yes' : 'No'));
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Authentication test failed: ' . $e->getMessage());
        }
    }

    private function testMessageSending()
    {
        $this->info('📱 Testing Message Sending...');

        $phoneNumber = $this->ask('Enter phone number for test', '966500681066');
        $messageText = $this->ask('Enter test message', 'Test message from diagnosis');

        try {
            // إنشاء رسالة اختبار
            $message = Message::create([
                'phone_number' => $phoneNumber,
                'message_text' => $messageText,
                'status' => 'pending',
                'message_type' => 'text'
            ]);

            $this->line("✅ Test message created with ID: {$message->id}");

            // إرسال Job
            dispatch(new \App\Jobs\SendMessageJob($message->id));
            $this->line('✅ SendMessageJob dispatched');

            // انتظار قصير ثم فحص النتيجة
            sleep(5);
            $message->refresh();

            $this->line("📊 Final status: {$message->status}");
            if ($message->error_message) {
                $this->error("   Error: {$message->error_message}");
            }
            if ($message->sent_at) {
                $this->line("   Sent at: {$message->sent_at}");
            }

        } catch (\Exception $e) {
            $this->error('❌ Message sending test failed: ' . $e->getMessage());
        }
    }
}
