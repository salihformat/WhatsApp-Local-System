<?php
$data = json_encode([
    'local_message_id' => 1,
    'status' => 'sent'
]);
$ch = curl_init('http://localhost/whatsapp-local-system/public/api/webhook/status');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer test_security_token_123',
    'Accept: application/json'
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status: $code\nResponse: $res\n";
