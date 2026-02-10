<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiseaseDetection; // Import Model
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    public function upload(Request $request)
    {
        // 1. Kiểm tra xem có ảnh không
        if ($request->hasFile('image')) {

            try {
                // --- BƯỚC A: LƯU ẢNH VÀO Ổ CỨNG ---
                $path = $request->file('image')->store('uploads', 'public');
                $fullUrl = url("storage/$path");

                // --- BƯỚC B: LƯU THÔNG TIN VÀO DATABASE NGAY LẬP TỨC ---
                $detection = DiseaseDetection::create([
                    'farm_id'      => $request->input('farm_id', 1), // Mặc định là Farm số 1
                    'image_url'    => $fullUrl,                      // Link ảnh vừa tạo
                    'disease_name' => $request->input('disease_name', 'Chưa xác định'),
                    'confidence'   => $request->input('confidence', 0),
                    'detected_at'  => now(),                         // Thời gian hiện tại
                    'temperature'  => $request->input('temperature', null),
                    'humidity'     => $request->input('humidity', null),
                ]);

                // --- BƯỚC C: TRẢ KẾT QUẢ ---
                return response()->json([
                    'success' => true,
                    'message' => 'Đã lưu ảnh và ghi vào Database thành công!',
                    'data'    => $detection
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi lưu DB: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json(['error' => 'Không tìm thấy file ảnh'], 400);
    }
}
