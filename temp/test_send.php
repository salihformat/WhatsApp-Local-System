<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\CentralApiService::class);

$message = App\Models\Message::create([
    'is_incoming' => 0,
    'user_id' => 8,
    'phone_number' => '966500681066',
    'message_text' => 'Hello from local testing',
    'message_type' => 'text',
    'status' => 'pending'
]);

$result = $service->sendMessage($message, 1);
echo "Send result:\n";
print_r($result);
