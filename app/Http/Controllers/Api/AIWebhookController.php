<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiseaseDetection;
use App\Models\Farm;

class AIWebhookController extends Controller
{
    // Hàm này sẽ nhận JSON từ Python gửi sang
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'farm_id' => 'required|integer',
            'image_url' => 'required|url',
            'disease_name' => 'required|string',
            'confidence' => 'required|numeric',
        ]);

        // 2. Lưu vào Database MySQL
        $alert = DiseaseDetection::create([
            'farm_id' => $validated['farm_id'],
            'image_url' => $validated['image_url'],
            'disease_name' => $validated['disease_name'],
            'confidence' => $validated['confidence'],
            'temperature' => $request->input('temperature', 0), // Nếu không gửi thì mặc định 0
            'humidity' => $request->input('humidity', 0),
            'detected_at' => now(),
        ]);

        // 3. Trả về thông báo thành công cho Python biết
        return response()->json([
            'status' => 'success',
            'message' => 'Đã lưu cảnh báo bệnh thành công!',
            'data_id' => $alert->id
        ], 201);
    }
}
