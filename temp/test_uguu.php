<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = storage_path('app/public/test.txt');
file_put_contents($filePath, 'Hello World');
$response = \Illuminate\Support\Facades\Http::attach('files[]', fopen($filePath, 'r'))->post('https://uguu.se/upload.php');
echo $response->body();
