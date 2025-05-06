<?php


namespace App\Http\Controllers;

use App\Services\SendOtpWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


class StudentController extends Controller
{
    public function index()
    {
        return view('students.table');
    }

    public function uploadExcel(Request $request)
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $data = Excel::toArray([], $request->file('excel_file'))[0];
            $headers = $data[0];
            $rows = array_slice($data, 1);
            $students = collect($rows)->map(fn($row) => array_combine($headers, $row));

            return view('students.table', compact('students', 'headers'));
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء قراءة الملف. يرجى التأكد من أن الملف صحيح وبصيغة Excel (xls, xlsx, csv)');
        }
    }
    public function send(Request $request)
    {
        if(Auth::user()){

            try {

                $template = $request->input('template');
                $tokenSingle = $request->input('token');
                $students = $request->input('students');
                $selected = $request->input('selected') ?? [];
                $phoneColumn = $request->input('phone_column');
                $method = $request->input('method');
                $otpService = new SendOtpWhatsapp($tokenSingle);

                $results = [];

                foreach ($selected as $i => $_) {
                    $student = json_decode($students[$i], true);


                    $message = preg_replace_callback('/\$(\w+)/u', function ($m) use ($student) {
                        return $student[$m[1]] ?? '';
                    }, $template);

                    $phone = preg_replace('/[^0-9]/', '', $student[$phoneColumn] ?? '');
                    if (!$phone) continue;

                    if($method == "allMessage"){
                        $sendResponse = $otpService->sendOtpSingle($phone, $message);
                    }

                    if($method == "ultramsg"){
                       $sendResponse = $otpService->sendUltraMsg($phone, $message);
                    }

//                    $otpService = new SendOtpWhatsapp();
//                    $sendResponse = $otpService->sendMediaMessage($phone, $message, 'https://picsum.photos/seed/picsum/600/400');


//                    Log::info('Send for ' . $sendResponse);
                    if ($sendResponse->successful()) {
                        $results[] = [
                            'phone' => $phone,
                            'message' => $message,
                            'status' => '✅ تم الإرسال بنجاح'
                        ];
                    }
                }
            }catch (\Exception $e) {
                    $results[] = [
                        'phone' => $phone,
                        'message' => $message,
                        'status' => '❌ Exception: ' . $e->getMessage()
                    ];
            }
            return view('students.result', compact('results'));

        }
        return route('login');
    }

}

