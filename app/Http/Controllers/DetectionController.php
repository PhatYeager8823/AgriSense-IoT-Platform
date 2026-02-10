<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiseaseDetection; // ✅ Đã có Model của bạn
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    // API 1: Upload ảnh (Python gọi bước 1)
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            // Trả về link đầy đủ để Python lấy gửi lại ở bước 2
            return response()->json([
                'success' => true,
                'url' => url("storage/$path")
            ]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    // API 2: Lưu dữ liệu (Python gọi bước 2)
    public function storeDetection(Request $request)
    {
        try {
            // Lấy dữ liệu Python gửi lên
            $data = $request->validate([
                'farm_id' => 'required|integer',
                'image_url' => 'required|string',
                'disease_name' => 'required|string',
                'confidence' => 'required|numeric',
            ]);

            // ✅ Dùng Model DiseaseDetection của bạn để lưu
            $detection = DiseaseDetection::create([
                'farm_id' => $data['farm_id'],
                'image_url' => $data['image_url'],
                'disease_name' => $data['disease_name'],
                'confidence' => $data['confidence'],
                'detected_at' => now(), // Thời gian hiện tại
                // temperature và humidity để null hoặc nhận từ request nếu có
                'temperature' => $request->input('temperature', null),
                'humidity' => $request->input('humidity', null),
            ]);

            return response()->json([
                'message' => 'Lưu thành công!',
                'data' => $detection
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
