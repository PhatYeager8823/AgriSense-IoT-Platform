<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    // Từ điển (Giữ nguyên)
    private $dictionary = [
        'Healthy' => 'Cây Khỏe Mạnh', 'Early Blight' => 'Nấm Đốm Vòng', 'Late Blight' => 'Nấm Sương Mai',
        'Leaf Miner' => 'Sâu Vẽ Bùa', 'Leaf_Miner' => 'Sâu Vẽ Bùa', 'Yellow Leaf Curl Virus' => 'Virus Xoăn Vàng Lá',
        'Mosaic Virus' => 'Virus Khảm Lá', 'Spider Mites' => 'Nhện Đỏ', 'Septoria' => 'Đốm Lá Septoria',
        'Bacterial Spot' => 'Đốm Vi Khuẩn', 'Powdery Mildew' => 'Phấn Trắng'
    ];

    public function index()
    {
        // 1. Lấy TẤT CẢ các loại bệnh từng phát hiện (Bỏ Carbon::today())
        $rawDiseases = DB::table('disease_detections')
            ->select(
                'disease_name',
                DB::raw('MAX(image_url) as image_url'),       // Lấy ảnh đại diện mới nhất
                DB::raw('COUNT(*) as total_count'),           // Đếm tổng số lần bị
                DB::raw('MAX(detected_at) as last_detected')  // Lấy thời gian phát hiện gần nhất
            )
            ->where('disease_name', '!=', 'Healthy')
            ->where('disease_name', '!=', 'Cây Khỏe Mạnh')
            // ->whereDate('detected_at', Carbon::today()) // <--- ĐÃ XÓA DÒNG NÀY ĐỂ KHÔNG BỊ MẤT BỆNH CŨ
            ->groupBy('disease_name')
            ->orderBy('last_detected', 'desc') // Sắp xếp: Bệnh nào mới gặp thì lên đầu
            ->get();

        // 2. Dịch sang tiếng Việt
        $detectedDiseases = $rawDiseases->map(function ($item) {
            $item->disease_name_vi = $this->dictionary[$item->disease_name] ?? $item->disease_name;
            return $item;
        });

        // 3. Lấy lịch sử chat
        $chatHistory = DB::table('chat_histories')->orderBy('created_at', 'asc')->get();

        return view('monitoring.ai_consultant', compact('detectedDiseases', 'chatHistory'));
    }

    public function askGemini(Request $request)
    {
        $apiKeys = [
            'AIzaSyA_uye2qbaKhO0KtSU6ySrNmUm3oQMBlrM', // Key 1
            'AIzaSyDyBepSnABaAv08aQqaLGlHPPp8bhiEgfc', // Key 2
            'AIzaSyBxGuBCV0tjVIr7f1vAbmWgNEi1srQf50c', // Key 3
            'AIzaSyA85EtVlta5o2KDkQrOKsEIZKug1y8D4_o', // Key 4
            'AIzaSyAkdlmCHOto3wkT98bKjSYnc-6A6NrfJP0', // Key 5
        ];

        // Lệnh này sẽ bốc bừa 1 key trong đống trên để dùng
        $apiKey = $apiKeys[array_rand($apiKeys)];
        $model = 'gemini-2.5-flash';

        $diseaseName = $request->input('disease');
        $userQuestion = $request->input('question');
        $contextDisease = $request->input('context');

        $finalUserMessage = "";

        // --- XỬ LÝ PROMPT ---
        if ($diseaseName) {
            // Thêm chữ "Cây trồng/Nông nghiệp" thật rõ để AI không hiểu nhầm là bệnh người
            $prompt = "Cây cà chua nông nghiệp bị bệnh: '$diseaseName'.
            Đóng vai kỹ sư nông nghiệp, trả lời ngắn gọn 3 ý:
            1. Nguyên nhân? 2. Dấu hiệu? 3. Cách trị (ưu tiên sinh học)?
            Trả lời tiếng Việt, gạch đầu dòng.";

            $finalUserMessage = "Tư vấn giúp tôi về bệnh: " . $diseaseName;
            $contextDisease = $diseaseName;
        }
        elseif ($userQuestion && $contextDisease) {
            $prompt = "Bạn là kỹ sư nông nghiệp. Người dùng đang hỏi về bệnh cây trồng: '$contextDisease'.
            Câu hỏi: '$userQuestion'. Hãy trả lời dựa trên ngữ cảnh bệnh '$contextDisease'.";
            $finalUserMessage = $userQuestion;
        }
        else {
            $prompt = "Bạn là Trợ lý Nông nghiệp AgriSense. Người dùng hỏi: '$userQuestion'. Hãy trả lời ngắn gọn.";
            $finalUserMessage = $userQuestion;
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [[ 'parts' => [['text' => $prompt]] ]],

                // --- CẤU HÌNH MỚI: TẮT BỘ LỌC AN TOÀN ---
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                ],

                // Giới hạn Token cao để không bị cắt chữ
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 10000,
                ]
            ]);

            $data = $response->json();

            // Debug lỗi nếu AI chặn câu trả lời
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                // Kiểm tra xem có phải do Safety Filter chặn không
                if (isset($data['promptFeedback'])) {
                    return response()->json(['answer' => "AI từ chối trả lời vì lý do an toàn (Safety Filter). Vui lòng thử lại câu hỏi cụ thể hơn về cây trồng."]);
                }
                return response()->json(['answer' => "Lỗi AI: Không nhận được phản hồi. Chi tiết: " . json_encode($data)]);
            }

            $rawAnswer = $data['candidates'][0]['content']['parts'][0]['text'];
            $formattedAnswer = nl2br(str_replace('**', '', $rawAnswer));

            // Lưu vào DB
            DB::table('chat_histories')->insert([
                'user_message' => $finalUserMessage,
                'bot_response' => $formattedAnswer,
                'context' => $contextDisease,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['answer' => $formattedAnswer, 'context' => $contextDisease]);

        } catch (\Exception $e) {
            return response()->json(['answer' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    // [MỚI] Hàm xóa lịch sử
    public function clearHistory()
    {
        DB::table('chat_histories')->truncate(); // Xóa sạch bảng
        return response()->json(['status' => 'success']);
    }
}
