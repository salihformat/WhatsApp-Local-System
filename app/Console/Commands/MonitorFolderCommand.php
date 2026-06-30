<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Log;

class MonitorFolderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:folder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor a specific folder for new files, extract phone numbers from filenames, and send them via WhatsApp.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        
        if (!File::exists($folderPath)) {
            $this->error("The folder {$folderPath} does not exist. Creating it now...");
            File::makeDirectory($folderPath, 0755, true);
        }

        $archivePath = $folderPath . '/archive';
        if (!File::exists($archivePath)) {
            File::makeDirectory($archivePath, 0755, true);
        }

        $failedPath = $folderPath . '/failed';
        if (!File::exists($failedPath)) {
            File::makeDirectory($failedPath, 0755, true);
        }

        $processingPath = $folderPath . '/processing';
        if (!File::exists($processingPath)) {
            File::makeDirectory($processingPath, 0755, true);
        }

        // Load synced central storage policy and settings if available
        $policy = null;
        if (!Storage::disk('local')->exists('local_system_config.json')) {
            $this->info("Local config cache not found. Automatically syncing configuration from central...");
            try {
                $this->call('local-system:sync-config');
            } catch (\Exception $e) {
                Log::error("Failed to run sync command programmatically: " . $e->getMessage());
            }
        }

        if (Storage::disk('local')->exists('local_system_config.json')) {
            $configData = json_decode(Storage::disk('local')->get('local_system_config.json'), true);
            $policy = $configData['storage_policy'] ?? null;
            $this->info("Loaded synced central storage policy: Max size " . ($policy['max_file_size_mb'] ?? 'N/A') . "MB");
        }

        $this->info("Scanning folder: {$folderPath}...");

        $files = File::files($folderPath);
        $processedCount = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $extension = strtolower($file->getExtension());
            $fullPath = $file->getPathname();

            // Ignore hidden files or files starting with .
            if (str_starts_with($filename, '.')) {
                continue;
            }

            // Strictly skip, archive, or delete binary printer spool files (.bin) to prevent sending raw files
            if ($extension === 'bin') {
                $this->info("Skipping printer binary spool file: {$filename}");
                try {
                    $targetArchive = $archivePath . '/' . $filename;
                    if (File::exists($targetArchive)) {
                        File::delete($fullPath);
                    } else {
                        File::move($fullPath, $targetArchive);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to archive bin file {$filename}: " . $e->getMessage());
                }
                continue;
            }

            // Only allow configured file extensions (pdf, docx, xlsx, txt, images). Archive/delete any unallowed extensions immediately.
            $allowedTypesStr = env('FILES_ALLOWED_TYPES', 'pdf,jpg,jpeg,png,doc,docx,xlsx,xls,csv,txt');
            $allowedExtensions = array_map('trim', explode(',', strtolower($allowedTypesStr)));
            
            if (!in_array($extension, $allowedExtensions)) {
                $this->info("Skipping unallowed file extension: {$filename}");
                try {
                    $targetFailed = $failedPath . '/' . $filename;
                    if (File::exists($targetFailed)) {
                        File::delete($fullPath);
                    } else {
                        File::move($fullPath, $targetFailed);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to handle unallowed file {$filename}: " . $e->getMessage());
                }
                continue;
            }

            // Central storage policy dynamic validation
            if ($policy) {
                // Check Max File Size
                $maxSizeMb = $policy['max_file_size_mb'] ?? 50;
                $fileSizeMb = $file->getSize() / (1024 * 1024);
                if ($fileSizeMb > $maxSizeMb) {
                    $this->warn("File {$filename} exceeds central max allowed size of {$maxSizeMb}MB (File size: " . round($fileSizeMb, 2) . "MB). Moving to failed.");
                    try {
                        File::move($fullPath, $failedPath . '/' . $filename);
                    } catch (\Exception $e) {
                        Log::error("Failed to move oversized file {$filename}: " . $e->getMessage());
                    }
                    continue;
                }

                // Check Allowed Mime Types
                $allowedMimes = $policy['allowed_mimes'] ?? [];
                if (!empty($allowedMimes)) {
                    $fileMime = File::mimeType($fullPath);
                    $fileExt = strtolower($file->getExtension());
                    if (!in_array($fileMime, $allowedMimes) && !in_array($fileExt, $allowedMimes)) {
                        $this->warn("File {$filename} type '{$fileMime}' (extension '{$fileExt}') is not allowed by central storage policy. Moving to failed.");
                        try {
                            File::move($fullPath, $failedPath . '/' . $filename);
                        } catch (\Exception $e) {
                            Log::error("Failed to move unallowed mime file {$filename}: " . $e->getMessage());
                        }
                        continue;
                    }
                }
            }

            // Extract phone number using regex (looks for a sequence of 9 to 15 digits)
            preg_match('/[0-9]{9,15}/', $filename, $matches);
            
            $phoneNumber = null;
            $extractedFromFilename = false;

            if (!empty($matches)) {
                $phoneNumber = $matches[0];
                $extractedFromFilename = true;
            } else {
                $fallbackPhone = env('MONITOR_FALLBACK_PHONE');
                if (!empty($fallbackPhone)) {
                    $phoneNumber = $fallbackPhone;
                    $this->info("No phone number found in filename '{$filename}'. Using fallback phone: {$phoneNumber}");
                }
            }

            if (!$phoneNumber) {
                $this->warn("No phone number found in filename: {$filename} and no fallback phone is configured. Moving to failed folder.");
                File::move($fullPath, $failedPath . '/' . $filename);
                continue;
            }

            $this->info("Processing file: {$filename} for phone: {$phoneNumber}");

            try {
                // Clean filename for WhatsApp display (remove the phone number and tidy delimiters)
                $cleanFilename = $filename;
                if ($extractedFromFilename) {
                    $cleanFilename = $this->cleanFilename($filename, $phoneNumber);
                }

                // Copy the file to public storage so it has a URL for the local system to use
                $baseName = pathinfo($filename, PATHINFO_FILENAME);
                if (mb_strlen($baseName) > 50) {
                    $baseName = mb_substr($baseName, 0, 50) . '_' . uniqid();
                }
                $publicFilename = time() . '_' . $baseName . '.' . $extension;
                $publicPath = 'attachments/' . $publicFilename;
                
                Storage::disk('public')->put($publicPath, File::get($fullPath));
                
                // Construct file URL safely as a relative path so it works from any host/domain
                $fileUrl = '/storage/' . $publicPath;

                $defaultText = env('MONITOR_MESSAGE_TEXT');
                if ($defaultText === null) {
                    $defaultText = 'مرفق لكم المستند المطلوب';
                }

                // Create message record
                $message = Message::create([
                    'phone_number' => $this->formatPhoneNumber($phoneNumber),
                    'message_text' => $defaultText,
                    'file_name' => $cleanFilename,
                    'file_path' => $fileUrl,
                    'file_type' => $this->getMimeTypeByExtension($extension),
                    'message_type' => 'media',
                    'status' => 'pending',
                    'created_at' => now()
                ]);

                // Move file immediately to processing folder to prevent reprocessing
                $processingFile = $processingPath . '/' . $filename;
                if (File::exists($processingFile)) {
                    File::delete($fullPath);
                } else {
                    File::move($fullPath, $processingFile);
                }

                $processedCount++;

                // Dispatch the job asynchronously to prevent single-threaded server deadlock
                dispatch(new SendMessageJob($message->id));

                Log::info("Dispatched file from PrintMonitor to queue", [
                    'filename' => $filename,
                    'phone' => $phoneNumber,
                    'message_id' => $message->id
                ]);

                $this->info("✅ File registered and queued for sending: {$filename}");
            } catch (\Exception $e) {
                $this->error("Failed to process file {$filename}: " . $e->getMessage());
                Log::error("PrintMonitor Error: " . $e->getMessage());
                
                // Move to failed on error
                $failedFile = $failedPath . '/' . $filename;
                if (!File::exists($failedFile)) {
                    File::move($fullPath, $failedFile);
                } else {
                    File::delete($fullPath);
                }
            }
        }

        $this->info("Scan complete. Processed {$processedCount} files.");
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '966' . substr($phoneNumber, 1);
        } elseif (strlen($phoneNumber) === 9) {
            $phoneNumber = '966' . $phoneNumber;
        }
        return $phoneNumber;
    }

    /**
     * Clean filename for WhatsApp display by removing phone numbers and trimming separators
     */
    protected function cleanFilename($filename, $phoneNumber)
    {
        // Remove phone number
        $cleaned = str_replace($phoneNumber, '', $filename);
        
        // Also clean up standard prefixes/suffixes like time() . '_'
        // Remove leading and trailing underscores, hyphens, and spaces
        $cleaned = preg_replace('/^[\s_\-]+|[\s_\-]+$/', '', $cleaned);
        
        // Replace double/multiple separators with a single one
        $cleaned = preg_replace('/[_\-]{2,}/', '_', $cleaned);

        $base = pathinfo($cleaned, PATHINFO_FILENAME);
        if (empty($base)) {
            return $filename; // fallback to original if cleaned is empty
        }

        return $cleaned;
    }

    /**
     * Get MIME type by file extension for robust sending on Windows
     */
    protected function getMimeTypeByExtension($extension)
    {
        $mimes = [
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        return $mimes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
