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
        // 1. Lấy TẤT CẢ các loại bệnh từng phát hiện
        $rawDiseases = DB::table('disease_detections')
            ->select(
                'disease_name',
                DB::raw('MAX(image_url) as image_url'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('MAX(detected_at) as last_detected')
            )
            ->where('disease_name', '!=', 'Healthy')
            ->where('disease_name', '!=', 'Cây Khỏe Mạnh')
            ->groupBy('disease_name')
            ->orderBy('last_detected', 'desc')
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
        // --- 1. KHAI BÁO DANH SÁCH KEY ---
        // Lưu ý: Đảm bảo trên Render bạn đặt tên biến là GEMINI_API_KEY_1, ..._2 (chứ không phải GEMINI_KEY_1)
        // Nếu trên Render đặt là GEMINI_KEY_1 thì sửa code dưới này lại cho khớp nhé.
        $apiKeys = [
            env('GEMINI_API_KEY_1'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
            env('GEMINI_API_KEY_4'),
            env('GEMINI_API_KEY_5'),
        ];

        // --- 2. SỬA LỖI TẠI ĐÂY (Dùng đúng biến $apiKeys) ---
        $availableKeys = array_filter($apiKeys); // Lọc bỏ key rỗng

        if (empty($availableKeys)) {
            return response()->json(['answer' => 'Lỗi Server: Chưa cấu hình API Key nào trong Environment Variables!'], 500);
        }

        // --- 3. CHỌN NGẪU NHIÊN KEY ---
        $apiKey = $availableKeys[array_rand($availableKeys)];

        // Model Gemini (Dùng bản Pro cho ổn định, hoặc Flash nếu muốn nhanh)
        $model = 'gemini-2.5-flash';

        $diseaseName = $request->input('disease');
        $userQuestion = $request->input('question');
        $contextDisease = $request->input('context');

        $finalUserMessage = "";
        $prompt = "";

        // --- XỬ LÝ PROMPT ---
        if ($diseaseName) {
            $prompt = "Bạn là kỹ sư nông nghiệp chuyên về cây cà chua. Cây đang bị bệnh: '$diseaseName'.
            Hãy tư vấn ngắn gọn 3 ý chính:
            1. Nguyên nhân?
            2. Dấu hiệu nhận biết?
            3. Cách điều trị (ưu tiên biện pháp sinh học)?
            Trả lời bằng tiếng Việt, trình bày gạch đầu dòng dễ đọc.";

            $finalUserMessage = "Tư vấn giúp tôi về bệnh: " . $diseaseName;
            $contextDisease = $diseaseName;
        }
        elseif ($userQuestion && $contextDisease) {
            $prompt = "Bạn là kỹ sư nông nghiệp. Đang nói về bệnh cây: '$contextDisease'.
            Người dùng hỏi thêm: '$userQuestion'. Hãy trả lời ngắn gọn, tập trung vào ngữ cảnh bệnh này.";
            $finalUserMessage = $userQuestion;
        }
        else {
            $prompt = "Bạn là Trợ lý Nông nghiệp AgriSense. Người dùng hỏi: '$userQuestion'. Hãy trả lời ngắn gọn, thân thiện.";
            $finalUserMessage = $userQuestion;
        }

        try {
            // Gửi request đến Google AI
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [[ 'parts' => [['text' => $prompt]] ]],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                ]
            ]);

            $data = $response->json();

            // Kiểm tra lỗi phản hồi từ Google
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                if (isset($data['error'])) {
                    return response()->json(['answer' => "Lỗi Google API: " . $data['error']['message']]);
                }
                return response()->json(['answer' => "AI đang bận, vui lòng thử lại sau giây lát."]);
            }

            $rawAnswer = $data['candidates'][0]['content']['parts'][0]['text'];

            // Format lại câu trả lời cho đẹp (Xóa dấu **, xuống dòng)
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

    public function clearHistory()
    {
        DB::table('chat_histories')->truncate();
        return response()->json(['status' => 'success']);
    }
}
