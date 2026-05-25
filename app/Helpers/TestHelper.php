<?php
// ملف: app/Helpers/TestHelper.php

namespace App\Helpers;

use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestHelper
{
    public static function testControllerDirectly($phoneNumber, $messageText, $fileName = null, $fileType = null)
    {
        $request = new Request();
        $request->merge([
            'phone_number' => $phoneNumber,
            'message_text' => $messageText,
            'file_name' => $fileName,
            'file_type' => $fileType
        ]);

        $controller = app(MessageController::class);
        return $controller->sendMessage($request);
    }

    public static function testViaHttp($phoneNumber, $messageText, $fileName = null, $fileType = null)
    {
        return Http::post('http://localhost:8001/api/send-message', [
            'phone_number' => $phoneNumber,
            'message_text' => $messageText,
            'file_name' => $fileName,
            'file_type' => $fileType
        ]);
    }

//    public static function testCentralApiDirectly($phoneNumber, $messageText, $fileName = null, $fileType = null)
//    {
//        return Http::withHeaders([
//            'X-Company-ID' => '1',
//            'Authorization' => 'Bearer test_security_token_123',
//            'Content-Type' => 'application/json'
//        ])->post('http://localhost:8000/api/messages/send', [
//            'phone_number' => $phoneNumber,
//            'message_text' => $messageText,
//            'file_content' => null,
//            'file_name' => $fileName,
//            'file_type' => $fileType,
//            'local_message_id' => null
//        ]);
//    }


    public static function testCentralApiDirectly($phoneNumber, $messageText, $fileName = null, $fileType = null)
    {
        return Http::withHeaders([
            'X-Company-ID' => config('app.company_id'),  // Use the configured company ID
            'Authorization' => 'Bearer '.config('app.central_api_token'),
            'Content-Type' => 'application/json'
        ])->post('http://localhost:8000/api/messages/send', [
            'phone_number' => $phoneNumber,
            'message_text' => $messageText,
            'file_content' => null,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'local_message_id' => null
        ]);
    }

}
