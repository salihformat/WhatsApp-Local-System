<?php
// ملف: app/Jobs/SendMessageJob.php (النظام المحلي)

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageId;

    // تحديد عدد المحاولات والوقت المسموح
    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 120, 300]; // إعادة المحاولة بعد 1، 2، 5 دقائق

    public function __construct($messageId)
    {
        $this->messageId = $messageId;
        Log::info("SendMessageJob created for message ID: {$messageId}");
    }

    public function handle(): void
    {
        Log::info("Processing SendMessageJob for message ID: {$this->messageId}");

        $message = Message::find($this->messageId);

        if (!$message) {
            Log::error("Message not found for ID: {$this->messageId}");
            return;
        }
// Log current configuration
        Log::info("Current configuration:", [
            'config_company_id' => config('app.company_id'),
            'env_company_id' => env('CENTRAL_API_COMPANY_ID'),
            'api_url' => config('app.central_api_url'),
            'message_id' => $this->messageId
        ]);

        // التحقق من حالة الرسالة
        if (!in_array($message->status, ['pending', 'failed'])) {
            Log::info("Message {$this->messageId} already processed with status: {$message->status}");
            return;
        }

        try {
            // تحديث حالة الرسالة إلى "processing"
            $message->update(['status' => 'processing']);

            // Prepare the request data
            $requestData = [
                'phone_number' => $message->phone_number,
                'local_message_id' => $message->id,
                'message_source' => 'local_system'
            ];

            // Attach dynamic provider details if specified in metadata
            $metadata = $message->metadata ?? [];
            if (isset($metadata['provider_code'])) {
                $requestData['provider_code'] = $metadata['provider_code'];
            }
            if (isset($metadata['provider_id'])) {
                $requestData['provider_id'] = $metadata['provider_id'];
            }
            if (isset($metadata['api_service_provider_id'])) {
                $requestData['api_service_provider_id'] = $metadata['api_service_provider_id'];
            }

            // Only add message if it's not empty and not a media message with caption
            if (!empty($message->message_text) && $message->message_type !== 'media') {
                $requestData['message'] = $message->message_text;
            }

            // Determine message type
            $requestData['type'] = 'text';

            // Add file information if this is a media message
            if ($message->message_type === 'media' && $message->file_path) {
                // Ensure we have the full URL for the file without duplicate storage prefixes
                $fileUrl = $message->file_path;
                if (preg_match('/^https?:\/\//i', $fileUrl)) {
                    // Already an absolute URL, but might have spaces/Arabic characters. Clean it.
                    $parts = parse_url($fileUrl);
                    $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
                    $host = $parts['host'] ?? '';
                    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
                    $path = $parts['path'] ?? '';
                    
                    $segments = explode('/', $path);
                    $encodedSegments = array_map(function($segment) {
                        return rawurlencode(rawurldecode($segment));
                    }, $segments);
                    $encodedPath = implode('/', $encodedSegments);
                    
                    $fileUrl = $scheme . $host . $port . $encodedPath;
                } else {
                    $cleanedPath = ltrim($fileUrl, '/');
                    if (str_starts_with($cleanedPath, 'storage/')) {
                        $relativePath = substr($cleanedPath, 8); // Remove 'storage/' prefix
                        $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($relativePath);
                    } else {
                        $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($fileUrl);
                    }
                    
                    // Safely encode non-ASCII/spaces in the newly built URL
                    if (preg_match('/^https?:\/\//i', $fileUrl)) {
                        $parts = parse_url($fileUrl);
                        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
                        $host = $parts['host'] ?? '';
                        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
                        $path = $parts['path'] ?? '';
                        
                        $segments = explode('/', $path);
                        $encodedSegments = array_map(function($segment) {
                            return rawurlencode(rawurldecode($segment));
                        }, $segments);
                        $encodedPath = implode('/', $encodedSegments);
                        
                        $fileUrl = $scheme . $host . $port . $encodedPath;
                    }
                }

                $requestData['file_url'] = $fileUrl;
                $requestData['file_name'] = $message->file_name;
                $requestData['file_type'] = $message->file_type;
                
                // Map file_type to WhatsApp API type
                $mime = strtolower($message->file_type);
                if (str_starts_with($mime, 'image/')) $requestData['type'] = 'image';
                elseif (str_starts_with($mime, 'video/')) $requestData['type'] = 'video';
                elseif (str_starts_with($mime, 'audio/')) $requestData['type'] = 'audio';
                else $requestData['type'] = 'document';
                
                // If there's a message and it's short, use it as a caption
                if (!empty($message->message_text) && mb_strlen($message->message_text) <= 1024) {
                    $requestData['message'] = $message->message_text;
                }

                Log::info('Prepared media message data', [
                    'file_url' => $fileUrl,
                    'file_name' => $message->file_name,
                    'file_type' => $message->file_type,
                    'type' => $requestData['type'],
                    'has_message' => isset($requestData['message'])
                ]);
            }

            Log::info('Sending message to central API', [
                'message_id' => $message->id,
                'message_type' => $message->message_type,
                'has_file' => !empty($message->file_path),
                'has_text' => isset($requestData['message'])
            ]);

            $request = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('app.central_api_token'),
                    'X-Company-ID' => config('app.company_id'),
                    'Accept' => 'application/json'
                ]);

            if (env('API_VERIFY_SSL', true) === false || env('API_VERIFY_SSL') === 'false') {
                $request->withoutVerifying();
            }

            $hasAttachedFile = false;
            if ($message->message_type === 'media' && $message->file_path) {
                $localPath = null;
                $filePathInDB = $message->file_path;
                
                if (filter_var($filePathInDB, FILTER_VALIDATE_URL)) {
                    $pathOnly = parse_url($filePathInDB, PHP_URL_PATH);
                } else {
                    $pathOnly = $filePathInDB;
                }
                
                if ($pathOnly) {
                    $cleanedPath = ltrim($pathOnly, '/');
                    if (str_starts_with($cleanedPath, 'storage/')) {
                        $cleanedPath = substr($cleanedPath, 8);
                    }
                    $localPath = storage_path('app/public/' . $cleanedPath);
                }

                if ($localPath && file_exists($localPath)) {
                    // Check if filename contains non-ASCII characters (like Arabic)
                    $isAscii = preg_match('/^[\x20-\x7E]*$/', $message->file_name);
                    
                    if ($isAscii) {
                        $uploadName = $message->file_name;
                    } else {
                        $extension = pathinfo($message->file_name, PATHINFO_EXTENSION);
                        $uploadName = 'document_' . $message->id . ($extension ? '.' . $extension : '');
                    }

                    $request = $request->attach(
                        'file',
                        fopen($localPath, 'r'),
                        $uploadName
                    );
                    $hasAttachedFile = true;
                    Log::info("Attached physical file for multipart upload: {$localPath} as {$uploadName}");
                    $requestData['file_url'] = null; // Ensure file_url is not set initially
                }
            }

            if ($hasAttachedFile) {
                // Try multipart upload first
                unset($requestData['file_url']);
                $response = $request->post(config('app.central_api_url') . '/messages/send', $requestData);

                // If multipart failed AND we have public temp storage enabled, try fallback
                if (!$response->successful() && env('USE_PUBLIC_TEMP_STORAGE', false) && isset($localPath) && file_exists($localPath)) {
                    Log::warning("Multipart upload failed with status " . $response->status() . ", attempting tmpfiles.org fallback...");
                    $publicUrl = $this->uploadToTemporaryPublicStorage($localPath);
                    
                    if ($publicUrl) {
                        $requestData['file_url'] = $publicUrl;
                        Log::info("Fallback successful: Uploaded file to public temp storage: {$publicUrl}");
                        
                        // Send as JSON with file_url
                        $request = Http::timeout(config('app.central_api_timeout', 60))
                            ->withToken(config('app.central_api_token'));
                        $response = $request->withHeaders(['Content-Type' => 'application/json'])
                            ->post(config('app.central_api_url') . '/messages/send', $requestData);
                    }
                }
            } else {
                $response = $request->withHeaders(['Content-Type' => 'application/json'])
                    ->post(config('app.central_api_url') . '/messages/send', $requestData);
            }

            if ($response->successful()) {
                $responseData = $response->json();

                $message->update([
                    'status' => $responseData['status'] ?? 'sent',
                    'sent_at' => now(),
                    'central_message_id' => $responseData['message_id'] ?? null,
                    'error_message' => null,
                ]);

                Log::info("Message {$this->messageId} sent successfully", [
                    'central_message_id' => $responseData['message_id'] ?? null
                ]);

                $this->moveFolderFile($message, true);

            } else {
                $this->handleFailedResponse($message, $response);
            }

        } catch (\Exception $e) {
            $this->handleException($message, $e);
        }
    }

    private function handleFailedResponse($message, $response)
    {
        $responseData = $response->json();
        $errorMessage = $responseData['message'] ?? 'خطأ غير معروف من النظام المركزي';

        // Convert array error messages to string
        if (is_array($errorMessage)) {
            $errorMessage = json_encode($errorMessage, JSON_UNESCAPED_UNICODE);
        }

        Log::error("Central API Error for message {$this->messageId}", [
            'status_code' => $response->status(),
            'response_body' => $response->body(),
            'attempt' => $this->attempts()
        ]);

        // إذا كان خطأ 429 (تجاوز الحد / Rate Limit)، نقوم بفحص Retry-After وإعادة الجدولة بذكاء
        if ($response->status() === 429) {
            $retryAfter = (int) $response->header('Retry-After', 60); // الافتراضي 60 ثانية
            Log::warning("Rate limit hit (429) for message {$this->messageId}. Retrying after {$retryAfter} seconds.");
            
            $message->update([
                'status' => 'failed',
                'error_message' => 'تجاوز حد الإرسال (Rate Limit)، سيتم إعادة المحاولة بعد ' . $retryAfter . ' ثانية',
            ]);

            $this->release($retryAfter);
            return;
        }

        // إذا كان خطأ 401 (مصادقة)، لا نعيد المحاولة
        if ($response->status() === 401) {
            $message->update([
                'status' => 'failed',
                'error_message' => 'خطأ في المصادقة: ' . $errorMessage,
            ]);
            $this->moveFolderFile($message, false);
            return;
        }

        // إذا كان خطأ 400 (بيانات خاطئة)، لا نعيد المحاولة
        if ($response->status() === 400) {
            $message->update([
                'status' => 'failed',
                'error_message' => 'بيانات خاطئة: ' . $errorMessage,
            ]);
            $this->moveFolderFile($message, false);
            return;
        }

        // للأخطاء الأخرى، نعيد المحاولة
        $message->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);

        if ($this->attempts() < $this->tries) {
            $this->release($this->backoff[$this->attempts() - 1] ?? 300);
        }
    }

    private function handleException($message, $exception)
    {
        Log::error("Exception in SendMessageJob for message {$this->messageId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'attempt' => $this->attempts()
        ]);

        $errorMessage = $exception->getMessage();
        if ($exception instanceof \Illuminate\Http\Client\ConnectionException || str_contains($errorMessage, 'cURL error') || str_contains($errorMessage, 'Timeout')) {
            $friendlyError = 'تعذر الاتصال بالسيرفر المركزي (انتهت مهلة الاتصال). يرجى التحقق من اتصال الإنترنت أو حالة السيرفر.';
        } else {
            $friendlyError = 'خطأ في الاتصال: ' . $errorMessage;
        }

        $message->update([
            'status' => 'failed',
            'error_message' => $friendlyError,
        ]);

        if ($this->attempts() < $this->tries) {
            $this->release($this->backoff[$this->attempts() - 1] ?? 300);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendMessageJob failed permanently for message {$this->messageId}", [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $message = Message::find($this->messageId);
        if ($message) {
            $errorMessage = $exception->getMessage();
            if ($exception instanceof \Illuminate\Http\Client\ConnectionException || str_contains($errorMessage, 'cURL error') || str_contains($errorMessage, 'Timeout')) {
                $friendlyError = 'فشل نهائي بعد ' . $this->tries . ' محاولات: تعذر الاتصال بالسيرفر المركزي.';
            } else {
                $friendlyError = 'فشل نهائي بعد ' . $this->tries . ' محاولات: ' . $errorMessage;
            }

            $message->update([
                'status' => 'failed',
                'error_message' => $friendlyError,
            ]);
            $this->moveFolderFile($message, false);
        }
    }

    /**
     * نقل ملف المراقبة بين مجلد المعالجة ومجلدات الأرشيف/الفشل
     */
    private function moveFolderFile($message, bool $success): void
    {
        if (empty($message->file_name)) {
            return;
        }

        $monitorFolder = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $processingFile = $monitorFolder . '/processing/' . $message->file_name;
        
        if (!\Illuminate\Support\Facades\File::exists($processingFile)) {
            // Search processing folder for files containing clean filename
            $processingDir = $monitorFolder . '/processing';
            if (\Illuminate\Support\Facades\File::exists($processingDir)) {
                $allFiles = \Illuminate\Support\Facades\File::files($processingDir);
                foreach ($allFiles as $f) {
                    $fn = $f->getFilename();
                    if ($fn === $message->file_name || str_contains($fn, $message->file_name)) {
                        $processingFile = $f->getPathname();
                        // Update message object file_name so it retains original name for target folder
                        $message->file_name = $fn;
                        break;
                    }
                }
            }
        }

        if (!\Illuminate\Support\Facades\File::exists($processingFile)) {
            Log::warning("Could not find file '{$message->file_name}' in processing folder to move.");
            return;
        }

        $targetFolder = $success ? 'archive' : 'failed';
        $targetPath = $monitorFolder . '/' . $targetFolder;

        if (!\Illuminate\Support\Facades\File::exists($targetPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($targetPath, 0755, true);
        }

        $targetFile = $targetPath . '/' . $message->file_name;

        // Automatically generate a unique name if the file already exists in the archive/failed folder
        if (\Illuminate\Support\Facades\File::exists($targetFile)) {
            $pathInfo = pathinfo($message->file_name);
            $baseName = $pathInfo['filename'];
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            
            $counter = 1;
            while (\Illuminate\Support\Facades\File::exists($targetPath . '/' . $baseName . '_' . $counter . $extension)) {
                $counter++;
            }
            $uniqueName = $baseName . '_' . $counter . $extension;
            $targetFile = $targetPath . '/' . $uniqueName;
            Log::info("File {$message->file_name} already exists in {$targetFolder}. Renaming archived copy to {$uniqueName}");
            $message->file_name = $uniqueName;
        }

        try {
            \Illuminate\Support\Facades\File::move($processingFile, $targetFile);
            Log::info("Moved file {$message->file_name} from processing to {$targetFolder}.");
        } catch (\Exception $e) {
            Log::error("Failed to move file {$message->file_name} to {$targetFolder}: " . $e->getMessage());
        }
    }

    /**
     * رفع الملف إلى خدمة استضافة عامة مؤقتة (لحل مشكلة سيرفرات الواتساب الخارجية التي تتطلب روابط عامة)
     */
    private function uploadToTemporaryPublicStorage(string $filePath): ?string
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://tmpfiles.org/api/v1/upload');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new \CURLFile($filePath)]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success' && isset($data['data']['url'])) {
                    $originalUrl = $data['data']['url'];
                    // Convert tmpfiles.org/XXXX to tmpfiles.org/dl/XXXX to get the direct download link
                    $directUrl = str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $originalUrl);
                    return $directUrl;
                }
            }
        } catch (\Exception $e) {
            Log::error('Temporary public upload failed: ' . $e->getMessage());
        }
        return null;
    }
}
