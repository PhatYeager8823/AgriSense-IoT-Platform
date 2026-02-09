<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Thư viện chuẩn để sau này đổi sang S3 dễ dàng

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Kiểm tra xem Python có gửi file tên là 'image' không
        if ($request->hasFile('image')) {

            // 1. Lưu ảnh (Code này dùng được cho cả Local và S3)
            // Hiện tại 'public' là lưu vào máy. Sau này đổi config thành 's3' là tự lên mây.
            $path = $request->file('image')->store('uploads', 'public');

            // 2. Lấy đường dẫn URL để trả về cho Python
            $url = Storage::disk('public')->url($path);

            return response()->json([
                'status' => 'success',
                'url' => $url // Python sẽ lấy link này để gửi tiếp vào API /ai-detect
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Chưa gửi file ảnh (key=image)'], 400);
    }
}
