<?php
$filePath = __DIR__.'/test.txt';
file_put_contents($filePath, 'Hello World');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://tmpfiles.org/api/v1/upload');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($filePath)]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo $response;
