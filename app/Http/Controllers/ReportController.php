<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::query();

        // التصفية حسب التاريخ
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        // التصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // التصفية حسب رقم الجوال
        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->input('phone_number') . '%');
        }

        // التصفية حسب الشركة (إذا كان لديك جدول companies محلي)
        // if ($request->has('company_id')) {
        //     $query->where('company_id', $request->input('company_id'));
        // }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function export(Request $request)
    {
        $query = Message::query();

        // تطبيق نفس فلاتر دالة index
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->input('phone_number') . '%');
        }

        $messages = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // إضافة رؤوس الأعمدة
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'رقم الجوال');
        $sheet->setCellValue('C1', 'نص الرسالة');
        $sheet->setCellValue('D1', 'اسم الملف');
        $sheet->setCellValue('E1', 'نوع الملف');
        $sheet->setCellValue('F1', 'الحالة');
        $sheet->setCellValue('G1', 'تاريخ الإرسال');
        $sheet->setCellValue('H1', 'عدد المحاولات');
        $sheet->setCellValue('I1', 'معرف الرسالة المركزي');
        $sheet->setCellValue('J1', 'رسالة الخطأ');
        $sheet->setCellValue('K1', 'تاريخ الإنشاء');

        // إضافة البيانات
        $row = 2;
        foreach ($messages as $message) {
            $sheet->setCellValue('A' . $row, $message->id);
            $sheet->setCellValue('B' . $row, $message->phone_number);
            $sheet->setCellValue('C' . $row, $message->message_text);
            $sheet->setCellValue('D' . $row, $message->file_name);
            $sheet->setCellValue('E' . $row, $message->file_type);
            $sheet->setCellValue('F' . $row, $message->status);
            $sheet->setCellValue('G' . $row, $message->sent_at);
            $sheet->setCellValue('H' . $row, $message->retry_count);
            $sheet->setCellValue('I' . $row, $message->central_message_id);
            $sheet->setCellValue('J' . $row, $message->error_message);
            $sheet->setCellValue('K' . $row, $message->created_at);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'whatsapp_messages_report_' . date('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * عرض صفحة تقارير أداء خدمة العملاء
     */
    public function performance(Request $request)
    {
        // إحصائيات عامة
        $totalConversations = \App\Models\Conversation::count();
        $openConversations = \App\Models\Conversation::where('status', 'open')->count();
        $closedConversations = \App\Models\Conversation::where('status', 'closed')->count();
        
        // المحادثات هذا الشهر
        $thisMonthConversations = \App\Models\Conversation::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count();

        // أداء الوكلاء (عدد المحادثات المغلقة لكل وكيل)
        $agentPerformance = \App\Models\User::withCount(['assignedConversations as closed_conversations_count' => function($q) {
            $q->where('status', 'closed');
        }])->having('closed_conversations_count', '>', 0)
          ->orderByDesc('closed_conversations_count')
          ->get();

        // إحصائيات الرسائل (الواردة والصادرة)
        $incomingMessages = Message::where('is_incoming', true)->count();
        $outgoingMessages = Message::where('is_incoming', false)->count();

        return view('reports.performance', compact(
            'totalConversations',
            'openConversations',
            'closedConversations',
            'thisMonthConversations',
            'agentPerformance',
            'incomingMessages',
            'outgoingMessages'
        ));
    }
}
