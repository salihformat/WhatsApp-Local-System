<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;
use App\Jobs\SendMessageJob;

// Create a dummy message
$message = Message::create([
    'phone_number' => '966500681066',
    'message_text' => 'test attachment script',
    'message_type' => 'media',
    'status' => 'pending',
    'file_name' => 'عروض اليوم الوطني.pdf',
    'file_type' => 'application/pdf',
    'file_path' => 'http://localhost:8006/storage/attachments/test_arabic.pdf'
]);

// create the physical file in storage so it finds it
$localPath = storage_path('app/public/attachments/test_arabic.pdf');
if (!file_exists(dirname($localPath))) {
    mkdir(dirname($localPath), 0755, true);
}
file_put_contents($localPath, 'dummy pdf content for testing');

echo 'Created message ID: ' . $message->id . PHP_EOL;

// Dispatch the job SYNCHRONOUSLY to see output
try {
    $job = new SendMessageJob($message->id);
    $job->handle();
    echo 'Job handled successfully' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Job failed: ' . $e->getMessage() . PHP_EOL;
}

$message->refresh();
echo 'Final Status: ' . $message->status . PHP_EOL;
if ($message->error_message) {
    echo 'Error: ' . $message->error_message . PHP_EOL;
}

