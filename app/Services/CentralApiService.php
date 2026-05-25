<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Message;
use Exception;

class CentralApiService
{
    private $baseUrl;
    private $companyId;
    private $apiToken;
    private $timeout;
    private $retryAttempts;
    private $retryDelay;

    public function __construct()
    {
        $this->baseUrl = config('app.central_api_url');
//        $this->companyId = config('app.central_api_company_id');
        $this->companyId = config('app.company_id');
        $this->apiToken = config('app.central_api_token');
        $this->timeout = config('app.central_api_timeout', 60);
        $this->retryAttempts = config('app.central_api_retry_attempts', 3);
        $this->retryDelay = config('app.central_api_retry_delay', 5);
    }

    /**
     * إرسال رسالة إلى النظام المركزي
     *
     * @param Message $message
     * @param int|null $companyId Company ID to use (overrides config if provided)
     * @return array
     */
    public function sendMessage(Message $message, ?int $companyId = null): array
    {
        $cacheKey = "sending_message_{$message->id}";

        // Use provided company ID or fall back to instance or config
        $companyId = $companyId ?? $this->companyId ?? config('app.company_id');

        Log::info("CentralApiService: Sending message", [
            'message_id' => $message->id,
            'company_id_used' => $companyId,
            'company_id_source' => $companyId === $this->companyId ? 'instance' : 'parameter',
            'config_company_id' => config('app.company_id')
        ]);

        // منع الإرسال المتكرر لنفس الرسالة
        if (Cache::has($cacheKey)) {
            return [
                'success' => false,
                'error' => 'الرسالة قيد الإرسال حالياً',
                'message_id' => null,
                'status' => 'duplicate'
            ];
        }

        Cache::put($cacheKey, true, 300); // 5 دقائق

        try {
            $data = $this->prepareMessageData($message);

            // Include company ID in the request data for tracking
            $data['company_id'] = $companyId;

            $result = $this->makeApiRequest('POST', '/messages/send', $data, $companyId);

            if ($result['success']) {
                // Update message with the result
                $message->update([
                    'status' => $result['status'] ?? 'sent',
                    'sent_at' => now(),
                    'central_message_id' => $result['message_id'] ?? null,
                    'error_message' => null,
                    'company_id' => $companyId  // Store the company ID used
                ]);

                Log::info("Message sent successfully", [
                    'message_id' => $message->id,
                    'central_message_id' => $result['message_id'] ?? null,
                    'status' => $result['status'] ?? 'sent',
                    'company_id' => $companyId
                ]);
            } else {
                // Handle error
                $errorMessage = $result['error'] ?? 'Unknown error';
                $message->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'company_id' => $companyId
                ]);

                Log::error("Failed to send message", [
                    'message_id' => $message->id,
                    'error' => $errorMessage,
                    'company_id' => $companyId
                ]);
            }

            return array_merge($result, ['message_id' => $message->id]);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            $message->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
                'company_id' => $companyId
            ]);

            Log::error("Exception while sending message", [
                'message_id' => $message->id,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString(),
                'company_id' => $companyId
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
                'message_id' => $message->id,
                'status' => 'failed'
            ];
        } finally {
            Cache::forget($cacheKey);
        }
    }

    /**
     * تحضير بيانات الرسالة للإرسال
     */
    private function prepareMessageData(Message $message): array
    {
        $data = [
            'phone_number' => $message->phone_number,
            'message_text' => $message->message_text,
            'local_message_id' => $message->id,
            'message_type' => $message->message_type,
        ];

        if ($message->hasFile()) {
            $filePath = storage_path('app/' . $message->file_path);

            if (!file_exists($filePath)) {
                throw new Exception("الملف غير موجود: {$filePath}");
            }

            $fileSize = filesize($filePath);
            $maxSize = config('app.files_max_size_mb', 20) * 1024 * 1024;

            if ($fileSize > $maxSize) {
                throw new Exception("حجم الملف يتجاوز الحد المسموح: " . round($fileSize / 1024 / 1024, 2) . "MB");
            }

            $fileContent = file_get_contents($filePath);
            $data['file_content'] = base64_encode($fileContent);
            $data['file_name'] = $message->file_name;
            $data['file_type'] = $message->file_type;
            $data['file_size'] = $fileSize;
        }

        return $data;
    }

    /**
     * تنفيذ طلب API إلى النظام المركزي
     */
    private function makeApiRequest(string $method, string $endpoint, array $data = [], ?int $companyId = null): array
    {
        $attempt = 0;
        $lastError = null;
        $currentCompanyId = $companyId ?? config('app.company_id');
        $requestId = uniqid('req_');

        Log::info("Making API request with company ID: " . $currentCompanyId, [
            'request_id' => $requestId,
            'endpoint' => $endpoint,
            'method' => $method,
            'company_id' => $currentCompanyId,

            'config_company_id' => config('app.company_id'),
            'env_company_id' => env('CENTRAL_API_COMPANY_ID'),
            'timestamp' => now()->toDateTimeString()
        ]);

        // Log request data (excluding sensitive information)
        $loggableData = $data;
        if (isset($loggableData['file_content'])) {
            $loggableData['file_content'] = '[BINARY DATA]';
        }
        Log::info("Request Data:", $loggableData);


        while ($attempt < $this->retryAttempts) {
            $attempt++;

            try {
                Log::info("API Request attempt {$attempt}", [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'data_size' => strlen(json_encode($data)),
                    'company_id' => $currentCompanyId,

                    'request_id' => $requestId,
                    'attempt' => "{$attempt}/{$this->retryAttempts}",
                    'timeout' => "{$this->timeout} seconds"

                ]);
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'X-Company-ID' => $currentCompanyId,  // Use the current config value
                    'Content-Type' => 'application/json',
                    'User-Agent' => config('app.api_user_agent', 'WhatsApp-Local-System/1.0'),
//                    'X-Request-ID' => uniqid('req_'),
                    'X-Request-ID' => $requestId,
                ])->timeout($this->timeout);
                $startTime = microtime(true);
                if ($method === 'POST') {
                    $httpResponse = $response->post($this->baseUrl . $endpoint, $data);
                } else {
                    $httpResponse = $response->get($this->baseUrl . $endpoint, $data);
                }

                $responseTime = round((microtime(true) - $startTime) * 1000, 2); // in milliseconds

                // Log response details
                Log::info("API Response", [
                    'request_id' => $requestId,
                    'status_code' => $httpResponse->status(),
                    'response_time' => "{$responseTime}ms",
                    'success' => $httpResponse->successful() ? 'Yes' : 'No',
                    'response_headers' => $httpResponse->headers(),
                    'response_body' => $httpResponse->json()
                ]);
                if ($httpResponse->successful()) {
                    $responseData = $httpResponse->json();

                    Log::info("API Request successful", [
                        'attempt' => $attempt,
                        'response_time' => $httpResponse->handlerStats()['total_time'] ?? 'unknown',

                        'request_id' => $requestId,
                        'message_id' => $responseData['message_id'] ?? null,
                        'status' => $responseData['status'] ?? null,
                        'provider_used' => $responseData['provider_used'] ?? null

                    ]);

                    return array_merge($responseData, [
                        'success' => $responseData['success'] ?? false,
                        'message_id' => $responseData['message_id'] ?? null,
                        'status' => $responseData['status'] ?? 'unknown',
                        'error' => $responseData['message'] ?? null,
                        'provider_used' => $responseData['provider_used'] ?? null,
                    ]);
                } else {
                    $errorData = $httpResponse->json();
                    $lastError = "HTTP {$httpResponse->status()}: " . ($errorData['message'] ?? 'Unknown error');

                    Log::warning("API Request failed", [
                        'attempt' => $attempt,
                        'status' => $httpResponse->status(),
                        'error' => $lastError,

                        'request_id' => $requestId,
                        'status_code' => $httpResponse->status(),
                        'response' => $errorData
                    ]);

                    // لا نعيد المحاولة في حالة أخطاء المصادقة أو البيانات
                    if (in_array($httpResponse->status(), [401, 403, 422])) {
                        Log::warning("Client error detected, not retrying", [
                            'request_id' => $requestId,
                            'status_code' => $httpResponse->status()
                        ]);
                        break;
                    }

                }

            } catch (Exception $e) {
                $lastError = $e->getMessage();

                Log::warning("API Request exception", [
                    'attempt' => $attempt,
                    'error' => $lastError
                ]);
            }

            // انتظار قبل إعادة المحاولة (إلا في المحاولة الأخيرة)
            if ($attempt < $this->retryAttempts) {
                sleep($this->retryDelay * $attempt); // تأخير متزايد
            }
        }

        return [
            'success' => false,
            'error' => $lastError ?? 'فشل في الاتصال بالنظام المركزي',
            'message_id' => null,
            'status' => 'failed'
        ];
    }

    /**
     * مزامنة حالات الرسائل مع النظام المركزي
     */
    public function syncMessageStatuses(array $messageIds): array
    {
        if (empty($messageIds)) {
            return [
                'success' => false,
                'error' => 'لا توجد رسائل للمزامنة'
            ];
        }

        try {
            $data = ['message_ids' => $messageIds];
            $result = $this->makeApiRequest('POST', '/messages/sync-status', $data);

            if ($result['success']) {
                return [
                    'success' => true,
                    'statuses' => $result['statuses'] ?? [],
                    'synced_count' => $result['synced_count'] ?? 0
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error']
                ];
            }

        } catch (Exception $e) {
            Log::error('Error syncing message statuses', [
                'message_ids' => $messageIds,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * التحقق من الاتصال بالنظام المركزي
     */
    public function checkConnection(): array
    {
        try {
            $result = $this->makeApiRequest('GET', '/health');

            return [
                'success' => $result['success'] ?? false,
                'response_time' => microtime(true) - LARAVEL_START,
                'message' => $result['error'] ?? 'الاتصال يعمل بشكل طبيعي'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'response_time' => null,
                'message' => 'فشل في الاتصال: ' . $e->getMessage()
            ];
        }
    }

    /**
     * الحصول على إحصائيات الشركة من النظام المركزي
     */
    public function getCompanyStatistics(): array
    {
        try {
            $result = $this->makeApiRequest('GET', '/statistics');

            if ($result['success']) {
                return [
                    'success' => true,
                    'statistics' => $result['statistics'] ?? [],
                    'company' => $result['company'] ?? []
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error']
                ];
            }

        } catch (Exception $e) {
            Log::error('Error getting company statistics', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
