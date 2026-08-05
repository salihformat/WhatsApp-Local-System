<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Contact;
use App\Models\Message;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

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

        $reviewPath = $folderPath . '/review';
        if (!File::exists($reviewPath)) {
            File::makeDirectory($reviewPath, 0755, true);
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
            $allowedExtensions = config('app.files_allowed_types', ['pdf','jpg','jpeg','png','doc','docx','xls','xlsx','csv','txt']);
            $allowedExtensions = array_map('trim', array_map('strtolower', $allowedExtensions));
            
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

            // Check Max File Size (use policy if available, otherwise use local config)
            $maxSizeMb = $policy['max_file_size_mb'] ?? (config('app.files_max_size', 20480) / 1024);
            $fileSizeMb = $file->getSize() / (1024 * 1024);
            if ($fileSizeMb > $maxSizeMb) {
                $this->warn("File {$filename} exceeds max allowed size of {$maxSizeMb}MB (File size: " . round($fileSizeMb, 2) . "MB). Moving to failed.");
                try {
                    File::move($fullPath, $failedPath . '/' . $filename);
                } catch (\Exception $e) {
                    Log::error("Failed to move oversized file {$filename}: " . $e->getMessage());
                }
                continue;
            }

            // Central storage policy dynamic validation
            if ($policy) {

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
            $trace = $this->newExtractionTrace();

            if (!empty($matches)) {
                $phoneNumber = $matches[0];
                $extractedFromFilename = true;
                $trace['source'] = 'filename';
            } elseif ($extension === 'pdf' && ($extractedFromContent = $this->extractPhoneFromPdfContent($fullPath, $filename, $trace))) {
                $phoneNumber = $extractedFromContent;
            } elseif ($extension === 'docx' && ($extractedFromContent = $this->extractPhoneFromDocxContent($fullPath, $filename, $trace))) {
                $phoneNumber = $extractedFromContent;
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png']) && ($extractedFromContent = $this->extractPhoneFromImageContent($fullPath, $filename, $trace))) {
                $phoneNumber = $extractedFromContent;
            } else {
                $fallbackPhone = env('MONITOR_FALLBACK_PHONE');
                if (!empty($fallbackPhone)) {
                    $phoneNumber = $fallbackPhone;
                    $trace['source'] = 'env_fallback';
                    $this->info("No phone number found in filename '{$filename}'. Using fallback phone: {$phoneNumber}");
                }
            }

            $trace['final_phone'] = $phoneNumber;
            if (empty($trace['source'])) {
                $trace['source'] = 'none';
            }
            $this->recordExtractionTrace($filename, $extension, $trace);

            if (!$phoneNumber) {
                $this->warn("No phone number found in filename: {$filename} and no fallback phone is configured. Moving to failed folder.");
                try {
                    $targetFailed = $failedPath . '/' . $filename;
                    if (File::exists($targetFailed)) {
                        File::delete($fullPath);
                    } else {
                        File::move($fullPath, $targetFailed);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to move file without phone number {$filename}: " . $e->getMessage());
                }
                continue;
            }

            // مطابقات مصدرها "احتياطي بلا تسمية" أو "طبقة نص تالفة" أو "رقم احتياطي عام" أقل موثوقية
            // من التسمية الصريحة أو رقم الملف المطابق لجهة اتصال — تُحجز للمراجعة اليدوية بدلاً من
            // الإرسال التلقائي، لتفادي تكرار حادثة إرسال ملف لرقم غير صحيح (قابلة للتخصيص من الإعدادات).
            $reviewRequiredSources = $this->getConfiguredList('PHONE_REVIEW_REQUIRED_SOURCES', ['unlabeled_fallback', 'corrupted_fallback', 'env_fallback']);
            if (in_array($trace['source'], $reviewRequiredSources, true)) {
                $this->warn("Phone number '{$phoneNumber}' for '{$filename}' extracted via low-confidence source '{$trace['source']}'. Holding for manual review instead of auto-sending.");
                $this->holdForManualReview($filename, $extension, $fullPath, $reviewPath, $phoneNumber, $extractedFromFilename);
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
                
                $storageFolder = trim(config('app.files_storage_path', 'invoices'), '/');
                $publicPath = $storageFolder . '/' . $publicFilename;
                
                Storage::disk('public')->put($publicPath, File::get($fullPath));
                
                // Construct file URL safely as a relative path so it works from any host/domain
                $fileUrl = '/storage/' . $publicPath;

                $messageText = null;
                $originalBaseName = pathinfo($filename, PATHINFO_FILENAME);

                // Try to extract message text from filename
                if ($extractedFromFilename && $phoneNumber) {
                    // First try exact match: PhoneNumber_Text
                    if (preg_match('/' . preg_quote($phoneNumber, '/') . '_(.+)$/u', $originalBaseName, $textMatches)) {
                        $messageText = trim($textMatches[1]);
                    } else {
                        // Fallback: remove phone number and clean ends
                        $textPart = str_replace($phoneNumber, '', $originalBaseName);
                        $textPart = preg_replace('/^[\s_\-]+|[\s_\-]+$/u', '', $textPart);
                        if (!empty($textPart)) {
                            $messageText = $textPart;
                        }
                    }
                }

                if (empty($messageText)) {
                    // Use MONITORING_MESSAGE_TEXT as requested by user, with backward compatibility for MONITOR_MESSAGE_TEXT
                    $messageText = env('MONITORING_MESSAGE_TEXT', env('MONITOR_MESSAGE_TEXT', 'مرفق لكم المستند المطلوب'));
                }

                // Create message record
                $message = Message::create([
                    'phone_number' => $this->formatPhoneNumber($phoneNumber),
                    'message_text' => $messageText,
                    'file_name' => $cleanFilename,
                    'source_filename' => $filename,
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
     * حجز ملف مع رقم جوال مستخرج من مصدر منخفض الثقة (احتياطي بلا تسمية / نص تالف / رقم احتياطي عام)
     * للمراجعة اليدوية بدلاً من إرساله تلقائياً. يُنشئ سجل رسالة بحالة review_pending دون إرساله فعلياً،
     * وينقل الملف لمجلد review بدلاً من processing حتى تتم الموافقة أو الرفض من صفحة متابعة الإرسال.
     */
    protected function holdForManualReview(string $filename, string $extension, string $fullPath, string $reviewPath, string $phoneNumber, bool $extractedFromFilename): void
    {
        try {
            $cleanFilename = $filename;
            if ($extractedFromFilename) {
                $cleanFilename = $this->cleanFilename($filename, $phoneNumber);
            }

            $baseName = pathinfo($filename, PATHINFO_FILENAME);
            if (mb_strlen($baseName) > 50) {
                $baseName = mb_substr($baseName, 0, 50) . '_' . uniqid();
            }
            $publicFilename = time() . '_' . $baseName . '.' . $extension;

            $storageFolder = trim(config('app.files_storage_path', 'invoices'), '/');
            $publicPath = $storageFolder . '/' . $publicFilename;

            Storage::disk('public')->put($publicPath, File::get($fullPath));
            $fileUrl = '/storage/' . $publicPath;

            $messageText = env('MONITORING_MESSAGE_TEXT', env('MONITOR_MESSAGE_TEXT', 'مرفق لكم المستند المطلوب'));

            $message = Message::create([
                'phone_number' => $this->formatPhoneNumber($phoneNumber),
                'message_text' => $messageText,
                'file_name' => $cleanFilename,
                'source_filename' => $filename,
                'file_path' => $fileUrl,
                'file_type' => $this->getMimeTypeByExtension($extension),
                'message_type' => 'media',
                'status' => 'review_pending',
                'created_at' => now(),
            ]);

            $reviewFile = $reviewPath . '/' . $filename;
            if (File::exists($reviewFile)) {
                File::delete($fullPath);
            } else {
                File::move($fullPath, $reviewFile);
            }

            Log::info("Held file from PrintMonitor for manual review (low-confidence phone extraction)", [
                'filename' => $filename,
                'phone' => $phoneNumber,
                'message_id' => $message->id,
            ]);

            $this->warn("⏸️ File held for manual review: {$filename} (phone: {$phoneNumber})");
        } catch (\Exception $e) {
            $this->error("Failed to hold file {$filename} for review: " . $e->getMessage());
            Log::error("PrintMonitor Error (holdForManualReview): " . $e->getMessage());
        }
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
     * استخراج النص من ملف PDF للبحث عن رقم الجوال
     */
    protected function extractPhoneFromPdfContent(string $filePath, string $filename, array &$trace): ?string
    {
        try {
            $text = (new PdfParser())->parseFile($filePath)->getText();
        } catch (\Exception $e) {
            Log::warning("MonitorFolder: Failed to parse PDF content for phone/file-number lookup: {$filename}", [
                'error' => $e->getMessage(),
            ]);
            $text = '';
        }

        if ($text !== '') {
            $phone = $this->extractPhoneFromText($text, $filename, $trace);
            if ($phone) {
                return $phone;
            }
        }

        // [Fix] احتياطي أخير: بعض ملفات PDF الممسوحة ضوئياً تخرج طبقة نصها تالفة بعطل ترميز خط
        // (حروف مشوّهة تماماً، وليست مجرد ترتيب معكوس يمكن إصلاحه — لوحظ فعلياً أن كلمة "File no"
        // تخرج كـ": no," بلا أي أثر لحرف "File") أو لا تحتوي طبقة نص أصلاً. في هاتين الحالتين لا
        // توجد أي معالجة نصية قادرة على استخراج الرقم لأن الحرف الصحيح غير موجود بالنص المستخرج
        // أصلاً. الحل: تحويل الصفحة الأولى لصورة عبر Ghostscript، ثم قراءتها بنفس محرك OCR
        // (Tesseract) المستخدم أصلاً للصور المرسلة مباشرة — يقرأ الصفحة كما تبدو بصرياً بغض النظر
        // عن أي عطل في طبقة النص الداخلية للملف.
        if (!filter_var(env('PDF_OCR_FALLBACK_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            if (empty($trace['source']) || $trace['source'] === 'no_match_in_content') {
                $trace['source'] = $trace['source'] ?: 'no_match_in_content';
            }
            return null;
        }

        $imagePath = $this->renderPdfPageToImage($filePath, $filename);
        if (!$imagePath) {
            return null;
        }

        try {
            $ocrPhone = $this->extractPhoneFromImageContent($imagePath, $filename, $trace);
            if ($ocrPhone) {
                $trace['pdf_ocr_used'] = true;
            }
            return $ocrPhone;
        } finally {
            @unlink($imagePath);
        }
    }

    /**
     * يحوّل الصفحة الأولى من ملف PDF إلى صورة PNG مؤقتة عبر Ghostscript، لاستخدامها كاحتياطي OCR
     * أخير عندما تفشل كل طرق قراءة النص المُستخرج مباشرة من الملف. يُعيد null بصمت إن لم يكن
     * Ghostscript مثبَّتاً أو فشل التحويل — هذا احتياطي اختياري، لا يُفشل معالجة الملف بأكملها.
     */
    protected function renderPdfPageToImage(string $pdfPath, string $filename): ?string
    {
        $gsPath = env('GHOSTSCRIPT_BIN_PATH');
        if (empty($gsPath) || !file_exists($gsPath)) {
            Log::debug("MonitorFolder: Ghostscript not configured/found, skipping PDF OCR fallback for '{$filename}'.");
            return null;
        }

        $outputDir = storage_path('app/' . trim(config('printing.download_path', 'print_jobs'), '/'));
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }
        $outputPath = $outputDir . '/ocr_' . uniqid() . '.png';

        try {
            $result = Process::timeout(30)->run([
                $gsPath,
                '-dNOPAUSE', '-dBATCH', '-dSAFER',
                '-sDEVICE=png16m',
                '-r200',
                '-dFirstPage=1', '-dLastPage=1',
                '-sOutputFile=' . $outputPath,
                $pdfPath,
            ]);
        } catch (\Exception $e) {
            Log::warning("MonitorFolder: Ghostscript execution failed for '{$filename}': " . $e->getMessage());
            return null;
        }

        if (!$result->successful() || !file_exists($outputPath)) {
            Log::warning("MonitorFolder: Failed to render PDF page to image for OCR fallback: {$filename}", [
                'error' => trim($result->errorOutput() ?: $result->output()),
            ]);
            return null;
        }

        return $outputPath;
    }

    /**
     * استخراج النص من ملف DOCX للبحث عن رقم الجوال
     */
    protected function extractPhoneFromDocxContent(string $filePath, string $filename, array &$trace): ?string
    {
        try {
            if (!class_exists('ZipArchive')) {
                Log::warning("MonitorFolder: ZipArchive class not found, cannot extract text from DOCX.");
                $trace['source'] = 'parse_error';
                return null;
            }

            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($xml !== false) {
                    $text = strip_tags($xml);
                    return $this->extractPhoneFromText($text, $filename, $trace);
                }
            }
        } catch (\Exception $e) {
            Log::warning("MonitorFolder: Failed to parse DOCX content for phone/file-number lookup: {$filename}", [
                'error' => $e->getMessage(),
            ]);
            $trace['source'] = 'parse_error';
        }
        return null;
    }

    /**
     * استخراج النص من صورة باستخدام Tesseract OCR
     */
    protected function extractPhoneFromImageContent(string $filePath, string $filename, array &$trace): ?string
    {
        try {
            if (!class_exists('\thiagoalessio\TesseractOCR\TesseractOCR')) {
                Log::warning("MonitorFolder: TesseractOCR class not found. OCR is not installed.");
                $trace['source'] = 'ocr_missing';
                return null;
            }

            $tesseract = new TesseractOCR($filePath);
            // دعم اللغتين العربية والإنجليزية
            $tesseract->lang('ara', 'eng');

            // إذا كان مسار Tesseract مخصصاً في الإعدادات
            $binPath = env('TESSERACT_BIN_PATH');
            if ($binPath) {
                $tesseract->executable($binPath);
            }

            $text = $tesseract->run();

            if (empty(trim($text))) {
                $trace['source'] = 'empty_image_text';
                return null;
            }

            return $this->extractPhoneFromText($text, $filename, $trace);

        } catch (\Exception $e) {
            Log::warning("MonitorFolder: Failed to run OCR on image {$filename}", [
                'error' => $e->getMessage(),
            ]);
            $trace['source'] = 'ocr_error';
            return null;
        }
    }

    /**
     * تحليل النص المستخرج من الملفات للبحث عن رقم الجوال أو رقم الملف.
     * $trace يُملأ أثناء التحليل بمعلومات القرار (المستوى المستخدم، الكلمة المطابقة، المطابقات المستبعدة)
     * لعرضها لاحقاً في صفحة متابعة الإرسال.
     */
    protected function extractPhoneFromText(string $text, string $filename, array &$trace): ?string
    {
        if (empty(trim($text))) {
            $trace['source'] = 'empty_text';
            return null;
        }

        // [New] كشف تلف ترميز النص
        $nonDigitChars = preg_replace('/[0-9\s]/', '', $text);
        if (mb_strlen($nonDigitChars) > 0) {
            $questionMarkRatio = mb_substr_count($nonDigitChars, '?') / mb_strlen($nonDigitChars);
            if ($questionMarkRatio > 0.3) {
                Log::warning("MonitorFolder: Text layer appears corrupted (possible missing Arabic font encoding) for '{$filename}'. Skipping labeled matching, trying digit-only fallback.", [
                    'question_mark_ratio' => round($questionMarkRatio, 2),
                ]);
                $this->warn("تحذير: طبقة النص في '{$filename}' تبدو تالفة (على الأرجح مشكلة ترميز خط عربي) — سيُكتفى بالبحث عن رقم يشبه جوال سعودي بلا تسمية.");

                $trace['source'] = 'corrupted_fallback';
                if (preg_match('/(?<!\d)(?:\+?966|0)?5[0-9]{8}(?!\d)/', $text, $m)) {
                    return $m[0];
                }
                return null;
            }
        }

        // 1) رقم جوال مُسمّى صراحة في النص (كلمات البحث قابلة للتخصيص من الإعدادات).
        // إذا وُجد المطابقة لكن في سياق مستبعد (مثل "هاتف المحل"/"جوال الشركة") يتم تجاهلها
        // والانتقال للمطابقة التالية، أو للمستوى الثاني (رقم الملف) إن لم يتبقَّ مطابقات صالحة.
        if ($phone = $this->findLabeledPhoneNumber($text, $filename, $trace)) {
            return $phone;
        }

        // 2) رقم ملف مُسمّى صراحة (رقم الملف / الملف رقم / file no، قابلة للتخصيص) → نبحث عنه في جهات الاتصال.
        // إذا وُجدت التسمية لكن دون جهة اتصال مطابقة، نتوقف هنا فوراً (لا ننتقل للتخمين في المستوى 3)
        // لأن وجود تسمية صريحة "رقم الملف" يعني أن الملف ذو صلة بجهة اتصال محددة وليس تخميناً عاماً.
        if ($phone = $this->findFileNumberPhone($text, $filename, $trace)) {
            return $phone;
        }
        if ($trace['source'] === 'file_number') {
            return null;
        }

        // 2.5) إعادة محاولة بعد تصحيح انعكاس ترتيب أحرف الكلمات العربية داخل النص (مشكلة معروفة في بعض
        // مستخرجات نص PDF تُخرج كل كلمة عربية بأحرف معكوسة بينما تُبقي الأرقام وترتيب الكلمات سليماً،
        // مثلاً "رقم الملف" تظهر كـ"فلملا مقر"). نجرب المطابقة مرة أخرى على نسخة مُصحَّحة من النص فقط
        // إذا فشلت المطابقة العادية ويحتوي النص على أحرف عربية أصلاً.
        if ($this->containsArabic($text)) {
            $reversedText = $this->reverseArabicWordLetters($text);
            if ($reversedText !== $text) {
                if ($phone = $this->findLabeledPhoneNumber($reversedText, $filename, $trace)) {
                    $trace['rtl_corrected'] = true;
                    return $phone;
                }

                if ($phone = $this->findFileNumberPhone($reversedText, $filename, $trace)) {
                    $trace['rtl_corrected'] = true;
                    return $phone;
                }
                if ($trace['source'] === 'file_number') {
                    $trace['rtl_corrected'] = true;
                    return null;
                }
            }
        }

        // 2.7) احتياطي مُتحقَّق من قاعدة البيانات: أي رقم يقع بالقرب من كلمة "no"/"رقم" (نمط أوسع بكثير
        // من قائمة FILE_NUMBER_LABELS المحدّدة، ويتحمل أخطاء OCR كحروف زائدة قبل الكلمة) يُعتبر
        // "مرشحاً" فقط، ولا يُقبل إلا إذا طابق فعلياً رقم ملف حقيقي موجود في جهات الاتصال. هذا يجعل
        // النمط الفضفاض آمناً تماماً: حتى لو التقط أرقاماً غير ذات صلة (رقم فاتورة/غرفة/هوية) فلن
        // تطابق أي جهة اتصال فعلية وتُتجاهل تلقائياً — الأمان يأتي من التحقق لا من دقة النمط.
        // أُضيف بعد اكتشاف حالة حقيقية (مستند ممسوح ضوئياً) حيث قرأ OCR التسمية "File No" بشكل جزئي
        // فقط (حروف "File" غير مقروءة بوضوح مهما تغيّرت جودة/دقة الصورة)، فلم تُطابق أي تسمية محدّدة.
        if ($contact = $this->findContactByLooseNumberCandidates($text)) {
            $trace['source'] = 'file_number_verified';
            $trace['file_number'] = $contact->file_number;
            $trace['contact_id'] = $contact->id;
            $this->info("Extracted phone via loose 'no/رقم' + contact-verified match for '{$filename}': file_number={$contact->file_number}");
            return $contact->phone_number;
        }

        // 3) احتياطي: رقم يشبه جوال سعودي فعلي (05xxxxxxxx / 9665xxxxxxxx / 5xxxxxxxx) بلا تسمية صريحة،
        // مع تطبيق نفس فحص كلمات الاستبعاد المستخدم في المستوى الأول حتى لا يلتقط هذا الاحتياطي رقماً
        // سبق استبعاده هناك (مثل رقم "Mobile no." الخاص بالمحل) عبر مسار مختلف.
        // ملاحظة: تم تقييد هذا الاحتياطي عمداً بعد اكتشاف أنه كان يلتقط أرقاماً عشوائية غير مرتبطة
        // بجوال (مثل أرقام تقارير/فواتير طويلة داخل المستند) ويرسل الملف لرقم خاطئ تماماً.
        // يمكن تعطيل هذا المستوى بالكامل عبر ENABLE_UNLABELED_PHONE_FALLBACK=false في الإعدادات.
        $unlabeledFallbackEnabled = filter_var(env('ENABLE_UNLABELED_PHONE_FALLBACK', true), FILTER_VALIDATE_BOOLEAN);
        if ($unlabeledFallbackEnabled && ($phone = $this->findUnlabeledPhoneNumber($text, $filename, $trace))) {
            return $phone;
        }

        $trace['source'] = 'no_match_in_content';
        return null;
    }

    /**
     * رقم ملف مُسمّى صراحة (رقم الملف / الملف رقم / file no، قابلة للتخصيص من الإعدادات) → نبحث عنه
     * في جهات الاتصال. يُعيد null سواء لم توجد التسمية أصلاً أو وُجدت لكن دون جهة اتصال مطابقة —
     * الفرق بين الحالتين يظهر في $trace['source'] === 'file_number' التي يفحصها المستدعي.
     */
    protected function findFileNumberPhone(string $text, string $filename, array &$trace): ?string
    {
        $fileNumberLabels = $this->getConfiguredList('FILE_NUMBER_LABELS', ['رقم الملف', 'الملف رقم', 'ملف رقم', 'file no', 'file number']);
        if (empty($fileNumberLabels)) {
            return null;
        }

        $labelPattern = $this->labelsToPattern($fileNumberLabels);

        if (preg_match('/(' . $labelPattern . ')\D{0,5}([0-9]+)/iu', $text, $m)) {
            $fileNumber = $m[2];
            $matchedLabel = $m[1];
        } elseif (preg_match('/([0-9]+)\D{0,5}(' . $labelPattern . ')/iu', $text, $m)) {
            // [Fix] نمط معكوس: الرقم قبل التسمية (مثلاً "72630File no.") — لوحظ فعلياً في بعض ملفات
            // PDF التي يعكس مستخرِج النص فيها ترتيب السطر بالكامل (وليس فقط أحرف الكلمات العربية
            // كما تعالجه reverseArabicWordLetters أعلاه)، فتخرج التسمية بعد الرقم بدل قبله.
            $fileNumber = $m[1];
            $matchedLabel = $m[2];
        } else {
            return null;
        }

        $trace['source'] = 'file_number';
        $trace['matched_label'] = trim($matchedLabel);
        $trace['file_number'] = $fileNumber;

        $contact = Contact::where('file_number', $fileNumber)->first();

        if ($contact) {
            $trace['contact_id'] = $contact->id;
            $this->info("Extracted file number '{$fileNumber}' from document content for '{$filename}', matched contact phone: {$contact->phone_number}");
            return $contact->phone_number;
        }

        Log::warning("MonitorFolder: File number '{$fileNumber}' found in document '{$filename}' but no matching contact.");
        $this->warn("Found file number '{$fileNumber}' in document content but no matching contact for '{$filename}'.");
        return null;
    }

    /**
     * يبحث عن كل الأرقام (رقمين فأكثر) القريبة من كلمة "no"/"رقم" في أي مكان بالنص (نافذة أحرف بسيطة
     * قبل/بعد، بلا اشتراط التصاق مباشر أو تسمية دقيقة) — نمط فضفاض عمداً ليتحمّل أخطاء OCR الشائعة
     * (مثل قراءة "File No" كـ"2No." أو ":No,"). الأمان يأتي من التحقق: كل مرشح يُفحص فوراً مقابل جدول
     * جهات الاتصال الفعلي، ولا يُقبل إلا إذا طابق رقم ملف حقيقي — أي مرشح خاطئ (رقم فاتورة/غرفة/هوية
     * صادف وجوده قرب كلمة "no") ببساطة لن يطابق أي جهة اتصال ويُتجاهل تلقائياً بلا أي مخاطرة.
     */
    protected function findContactByLooseNumberCandidates(string $text): ?Contact
    {
        $window = 8;

        if (!preg_match_all('/[0-9]{2,}/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($matches[0] as $match) {
            $number = $match[0];
            $start = $match[1];

            $contextStart = max(0, $start - $window);
            $contextBefore = substr($text, $contextStart, $start - $contextStart);
            $contextAfter = substr($text, $start + strlen($number), $window);

            $hasIndicator = stripos($contextBefore, 'no') !== false
                || stripos($contextAfter, 'no') !== false
                || mb_stripos($contextBefore, 'رقم') !== false
                || mb_stripos($contextAfter, 'رقم') !== false;

            if (!$hasIndicator) {
                continue;
            }

            $contact = Contact::where('file_number', $number)->first();
            if ($contact) {
                return $contact;
            }
        }

        return null;
    }

    /**
     * احتياطي بلا تسمية: يبحث عن كل الأرقام التي تشبه جوالاً سعودياً في النص، ويتجاهل أي مرشح يقع
     * ضمن سياق مستبعد (PHONE_EXTRACTION_EXCLUDE_CONTEXT) — قبله أو بعده، لأن الرقم هنا بلا تسمية
     * توجّه البحث لجهة واحدة كما في المستوى الأول — ويُعيد أول مرشح غير مستبعد.
     */
    protected function findUnlabeledPhoneNumber(string $text, string $filename, array &$trace): ?string
    {
        $excludeContext = $this->getConfiguredList('PHONE_EXTRACTION_EXCLUDE_CONTEXT', [
            'المحل', 'محلنا', 'الشركة', 'شركتنا', 'مكتبنا', 'الفرع', 'فرعنا', 'store', 'shop', 'company', 'branch',
        ]);
        $excludeWindow = (int) env('PHONE_EXTRACTION_EXCLUDE_WINDOW_CHARS', 40);

        if (!preg_match_all('/(?<!\d)(?:\+?966|0)?5[0-9]{8}(?!\d)/', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($matches[0] as $match) {
            $phone = $match[0];
            $matchStart = $match[1];

            if (!empty($excludeContext)) {
                $windowStart = max(0, $matchStart - $excludeWindow);
                $windowEnd = $matchStart + strlen($phone) + $excludeWindow;
                $context = substr($text, $windowStart, $windowEnd - $windowStart);

                $excludedBy = null;
                foreach ($excludeContext as $word) {
                    if ($word !== '' && mb_stripos($context, $word) !== false) {
                        $excludedBy = $word;
                        break;
                    }
                }

                if ($excludedBy !== null) {
                    $trace['excluded'][] = ['value' => $phone, 'matched_label' => null, 'excluded_by' => $excludedBy];
                    $this->info("Ignored unlabeled phone candidate '{$phone}' for '{$filename}': found near excluded context word '{$excludedBy}'.");
                    continue;
                }
            }

            $trace['source'] = 'unlabeled_fallback';
            $this->info("Extracted phone number from document content (unlabeled fallback, phone-pattern restricted) for '{$filename}': {$phone}");
            return $phone;
        }

        return null;
    }

    /**
     * هل يحتوي النص على أحرف عربية أصلاً؟ (فحص أولي قبل محاولة تصحيح انعكاس الأحرف، تجنباً لعبء غير
     * ضروري على نصوص إنجليزية بحتة).
     */
    protected function containsArabic(string $text): bool
    {
        return (bool) preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]/u', $text);
    }

    /**
     * تصحيح مشكلة معروفة في بعض مستخرجات نص PDF حيث تُستخرج كل كلمة عربية (كتلة أحرف عربية متصلة)
     * بترتيب أحرف معكوس، بينما تبقى الأرقام والمسافات وترتيب الكلمات نفسها سليمة. مثال حقيقي من هذا
     * النظام: "رقم الملف" تُستخرج كـ"فلملا مقر" — عكس أحرف كل كلمة عربية على حدة (وليس السلسلة كاملة،
     * وليس ترتيب الكلمات) يُعيدها لصورتها الصحيحة القابلة للمطابقة مع كلمات البحث المُعدّة في الإعدادات.
     */
    protected function reverseArabicWordLetters(string $text): string
    {
        return preg_replace_callback('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}]+/u', function ($m) {
            return implode('', array_reverse(mb_str_split($m[0])));
        }, $text);
    }

    /**
     * البحث عن رقم جوال مُسمّى صراحة في النص باستخدام كلمات دلالة قابلة للتخصيص (PHONE_EXTRACTION_LABELS)،
     * مع تجاهل أي مطابقة تقع في سياق مستبعد (PHONE_EXTRACTION_EXCLUDE_CONTEXT) مثل "هاتف المحل"/"جوال الشركة"
     * التي تدل على أن الرقم القريب ليس رقم العميل المقصود بل رقم الجهة المُصدرة للمستند نفسها.
     */
    protected function findLabeledPhoneNumber(string $text, string $filename, array &$trace): ?string
    {
        $labels = $this->getConfiguredList('PHONE_EXTRACTION_LABELS', ['رقم الجوال', 'الجوال', 'جوال', 'phone', 'mobile']);
        if (empty($labels)) {
            return null;
        }

        $excludeContext = $this->getConfiguredList('PHONE_EXTRACTION_EXCLUDE_CONTEXT', [
            'المحل', 'محلنا', 'الشركة', 'شركتنا', 'مكتبنا', 'الفرع', 'فرعنا', 'store', 'shop', 'company', 'branch',
        ]);
        $excludeWindow = (int) env('PHONE_EXTRACTION_EXCLUDE_WINDOW_CHARS', 40);

        if (!preg_match_all('/(' . $this->labelsToPattern($labels) . ')\D{0,10}([0-9]{9,15})/iu', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($matches[0] as $i => $fullMatch) {
            $matchStart = $fullMatch[1];
            $label = trim($matches[1][$i][0]);
            $phone = $matches[2][$i][0];

            if (!empty($excludeContext)) {
                // نافذة الفحص تشمل السياق قبل المطابقة + المطابقة نفسها (تسمية + الفجوة حتى الرقم)،
                // لأن كلمة استبعاد مثل "Mobile no" قد تحتوي كلمة البحث "mobile" ذاتها كجزء منها
                // (تظهر داخل نص المطابقة نفسه، وليس بالضرورة قبله)، فلا يكفي فحص ما قبل المطابقة فقط.
                $contextStart = max(0, $matchStart - $excludeWindow);
                $context = substr($text, $contextStart, ($matchStart + strlen($fullMatch[0])) - $contextStart);

                $excludedBy = null;
                foreach ($excludeContext as $word) {
                    if ($word !== '' && mb_stripos($context, $word) !== false) {
                        $excludedBy = $word;
                        break;
                    }
                }

                if ($excludedBy !== null) {
                    $trace['excluded'][] = ['value' => $phone, 'matched_label' => $label, 'excluded_by' => $excludedBy];
                    $this->info("Ignored phone match '{$phone}' for '{$filename}': found near excluded context word '{$excludedBy}' (likely the issuing party's own number, not the customer's).");
                    continue;
                }
            }

            $trace['source'] = 'label';
            $trace['matched_label'] = $label;
            $this->info("Extracted phone number from document content (labeled) for '{$filename}': {$phone}");
            return $phone;
        }

        return null;
    }

    /**
     * قراءة قائمة مفصولة بفواصل من متغيرات البيئة (إعدادات النظام)، مع قيمة افتراضية إذا لم تُضبط.
     * ضبط القيمة على سلسلة فارغة صراحة في الإعدادات يُعطّل هذا المستوى من البحث بالكامل.
     */
    protected function getConfiguredList(string $envKey, array $default): array
    {
        $raw = env($envKey);
        if ($raw === null) {
            return $default;
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw)), fn ($v) => $v !== ''));
    }

    /**
     * تحويل قائمة كلمات دلالة إلى نمط regex بديل (OR)، مع دعم وضعي المطابقة الجزئية والكاملة
     * عبر PHONE_MATCH_MODE=partial|exact (الكامل يفرض حدود كلمة \b حول كل كلمة دلالة).
     */
    protected function labelsToPattern(array $labels): string
    {
        $matchMode = env('PHONE_MATCH_MODE', 'partial');
        $quoted = array_map(fn ($l) => preg_quote($l, '/'), $labels);

        if ($matchMode === 'exact') {
            // \b الاعتيادية في PCRE مبنية على \w وهو ASCII فقط حتى مع محدّد /u، فلا يتعرف على حدود
            // الكلمات العربية بشكل صحيح ويكسر المطابقة تماماً لأي تسمية عربية. نستخدم بدلاً منه lookaround
            // يعتمد على \p{L} (حرف) فقط — وليس \p{N} أيضاً — لأن التسمية غالباً ما تُلاصق الرقم مباشرة
            // بلا فاصل (مثل "رقم الملف123")، وهذا مقصود ومطلوب مطابقته، وليس امتداداً لكلمة أطول.
            $quoted = array_map(fn ($l) => '(?<![\p{L}])' . $l . '(?![\p{L}])', $quoted);
        }

        return implode('|', $quoted);
    }

    /**
     * هيكل فارغ لسجل تتبّع قرار استخراج رقم الجوال، يُملأ أثناء المعالجة ثم يُحفظ عبر recordExtractionTrace().
     */
    protected function newExtractionTrace(): array
    {
        return [
            'source' => null,
            'matched_label' => null,
            'file_number' => null,
            'contact_id' => null,
            'excluded' => [],
            'final_phone' => null,
            'rtl_corrected' => false,
            'pdf_ocr_used' => false,
        ];
    }

    /**
     * حفظ سجل تتبّع قرار الاستخراج لكل ملف تمت معالجته (سواء نجح أو فشل استخراج رقم منه)،
     * ليُعرض لاحقاً في صفحة متابعة الإرسال ويوضّح للمستخدم "لماذا اتخذ النظام هذا القرار".
     */
    protected function recordExtractionTrace(string $filename, string $extension, array $trace): void
    {
        try {
            \App\Models\ExtractionTrace::create([
                'filename' => $filename,
                'extension' => $extension,
                'source' => $trace['source'] ?? 'none',
                'matched_label' => $trace['matched_label'] ?? null,
                'file_number' => $trace['file_number'] ?? null,
                'contact_id' => $trace['contact_id'] ?? null,
                'excluded' => !empty($trace['excluded']) ? $trace['excluded'] : null,
                'final_phone' => $trace['final_phone'] ?? null,
                'rtl_corrected' => $trace['rtl_corrected'] ?? false,
                'pdf_ocr_used' => $trace['pdf_ocr_used'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::warning("MonitorFolder: Failed to record extraction trace for '{$filename}': " . $e->getMessage());
        }
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
