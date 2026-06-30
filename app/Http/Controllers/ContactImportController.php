<?php

namespace App\Http\Controllers;

use App\Models\ContactImport;
use App\Models\ContactGroup;
use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactImportController extends Controller
{
    private ContactImportService $importService;

    public function __construct(ContactImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * صفحة الاستيراد + سجل العمليات السابقة
     */
    public function index()
    {
        $imports = ContactImport::forUser(auth()->id())->with('contactGroup')->latest()->paginate(10);
        $groups = ContactGroup::forUser(auth()->id())->active()->get();
        $availableFields = ContactImportService::AVAILABLE_FIELDS;

        return view('contacts.import', compact('imports', 'groups', 'availableFields'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'يرجى اختيار ملف',
            'file.mimes' => 'الصيغ المدعومة: xlsx, xls, csv',
            'file.max' => 'الحد الأقصى لحجم الملف: 10 ميجابايت',
        ]);

        try {
            $file = $request->file('file');
            
            // Generate a unique filename and save using Laravel Storage system
            $fileName = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('imports', $fileName, 'local');
            $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($path);

            $preview = $this->importService->previewFile($fullPath);

            $groups = ContactGroup::forUser(auth()->id())->active()->get();
            $availableFields = ContactImportService::AVAILABLE_FIELDS;

            return view('contacts.import-preview', [
                'headers' => $preview['headers'],
                'preview' => $preview['preview'],
                'total_rows' => $preview['total_rows'],
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'groups' => $groups,
                'availableFields' => $availableFields,
            ]);
        } catch (\Exception $e) {
            Log::error('File upload preview error', ['error' => $e->getMessage()]);
            return back()->with('error', 'خطأ في قراءة الملف: ' . $e->getMessage());
        }
    }

    /**
     * تنفيذ الاستيراد بعد المعاينة وربط الأعمدة
     */
    public function process(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
            'file_name' => 'required|string',
            'mapping' => 'required|array',
            'mapping.phone_number' => 'required',
            'mapping.name' => 'required',
            'duplicate_handling' => 'required|in:skip,update',
            'contact_group_id' => 'nullable|exists:contact_groups,id',
        ], [
            'mapping.phone_number.required' => 'يجب تحديد عمود رقم الهاتف',
            'mapping.name.required' => 'يجب تحديد عمود الاسم',
        ]);

        try {
            // إنشاء سجل الاستيراد
            $import = ContactImport::create([
                'user_id' => auth()->id(),
                'file_name' => $request->file_name,
                'file_path' => $request->file_path,
                'column_mapping' => $request->mapping,
                'contact_group_id' => $request->contact_group_id,
                'status' => 'pending',
            ]);

            // تنفيذ الاستيراد
            $results = $this->importService->processImport($import, $request->duplicate_handling);

            $message = "تم الاستيراد: {$results['success_count']} ناجحة";
            if ($results['duplicate_count'] > 0) $message .= " | {$results['duplicate_count']} مكررة";
            if ($results['updated_count'] > 0) $message .= " | {$results['updated_count']} محدّثة";
            if ($results['failed_count'] > 0) $message .= " | {$results['failed_count']} فاشلة";

            return redirect()->route('contacts.import.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Import process error', ['error' => $e->getMessage()]);
            return redirect()->route('contacts.import.index')->with('error', 'فشل الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * تحميل نموذج Excel
     */
    public function downloadTemplate()
    {
        try {
            $filePath = $this->importService->generateTemplate();
            return response()->download($filePath, 'contacts_template.xlsx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في إنشاء النموذج');
        }
    }
}
