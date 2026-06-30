<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = config('app.central_api_url') . '/messages/send';
$token = config('app.central_api_token');
$companyId = config('app.company_id');

echo 'Testing API: ' . $url . PHP_EOL;
echo 'Company ID: ' . $companyId . PHP_EOL;

$filePath = storage_path('app/public/test_attach.txt');
file_put_contents($filePath, 'Hello this is a test file.');

$requestData = [
    'phone_number' => '966500681066',
    'local_message_id' => 9999,
    'message_source' => 'local_system',
    'type' => 'document',
    'message' => 'test message from script',
    'file_name' => 'test_attach.txt',
    'file_type' => 'text/plain',
];

$request = \Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'X-Company-ID' => $companyId,
    'Accept' => 'application/json'
])->withoutVerifying();

$request = $request->attach('file', fopen($filePath, 'r'), 'test_attach.txt');

echo 'Sending request...' . PHP_EOL;
$response = $request->post($url, $requestData);

echo 'Status: ' . $response->status() . PHP_EOL;
echo 'Body: ' . $response->body() . PHP_EOL;
