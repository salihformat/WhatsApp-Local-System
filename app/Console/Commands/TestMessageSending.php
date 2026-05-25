<?php
// ملف: app/Console/Commands/TestMessageSending.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;

class TestMessageSending extends Command
{
    protected $signature = 'test:message {phone} {message} {--file=} {--provider=} {--provider-id=}';
    protected $description = 'Test message sending functionality';

    public function handle()
    {
        $phone = $this->argument('phone');
        $messageText = $this->argument('message');
        $file = $this->option('file');
        $provider = $this->option('provider');
        $providerId = $this->option('provider-id');

        $this->info("Testing message sending...");
        $this->info("Phone: {$phone}");
        $this->info("Message: {$messageText}");
        if ($file) {
            $this->info("File: {$file}");
        }
        if ($provider) {
            $this->info("Provider: {$provider}");
        }
        if ($providerId) {
            $this->info("Provider ID: {$providerId}");
        }

        // Format phone number
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '966' . substr($phoneNumber, 1);
        }

        // Build metadata
        $metadata = [];
        if ($provider) {
            $metadata['provider_code'] = $provider;
        }
        if ($providerId) {
            $metadata['provider_id'] = $providerId;
        }

        try {
            if ($file) {
                // If a file path or URL is provided, create a media message
                $filePath = $file;
                $fileName = basename($file);
                $fileType = 'application/pdf'; // Default fallback or look up extension
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $fileType = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
                } elseif (in_array($ext, ['mp4', 'avi', 'mov'])) {
                    $fileType = 'video/' . $ext;
                }

                $message = \App\Models\Message::create([
                    'phone_number' => $phoneNumber,
                    'message_text' => $messageText,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'message_type' => 'media',
                    'status' => 'pending',
                    'metadata' => $metadata,
                    'created_at' => now()
                ]);
            } else {
                $message = \App\Models\Message::create([
                    'phone_number' => $phoneNumber,
                    'message_text' => $messageText,
                    'message_type' => 'text',
                    'status' => 'pending',
                    'metadata' => $metadata,
                    'created_at' => now()
                ]);
            }

            $this->info("Message created in local database with ID: {$message->id}");
            $this->info("Executing SendMessageJob synchronously for immediate feedback...");

            // Process job synchronously for immediate test feedback
            $job = new \App\Jobs\SendMessageJob($message->id);
            $job->handle();

            // Refresh message state
            $message->refresh();

            $this->info("Final Status: " . $message->status);
            if (in_array($message->status, ['sent', 'processing', 'delivered'])) {
                $this->info("✅ Test passed!");
            } else {
                $this->error("❌ Test failed! Error: " . $message->error_message);
            }

        } catch (\Exception $e) {
            $this->error("❌ Exception occurred: " . $e->getMessage());
        }
    }
}
