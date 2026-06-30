<?php
$ch = curl_init('http://localhost/whatsapp-local-system/public/api/webhook/status');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status: $code\nResponse: $res\n";
