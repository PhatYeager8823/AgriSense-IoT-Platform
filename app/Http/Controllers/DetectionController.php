<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    public function upload(Request $request)
    {
        // 1. Kiểm tra xem Python có gửi file tên là 'image' không
        if ($request->hasFile('image')) {

            // 2. Lưu ảnh vào thư mục 'storage/app/public/uploads'
            // Nó sẽ tự sinh ra cái tên ngẫu nhiên (vd: h3n4k5...jpg) để không bị trùng
            $path = $request->file('image')->store('uploads', 'public');

            // 3. Trả về kết quả cho Python (kèm link ảnh)
            return response()->json([
                'success' => true,
                'message' => 'Nhận ảnh thành công!',
                'url' => url("storage/$path") // Link ảnh để xem
            ]);
        }

        // 4. Nếu không có ảnh thì báo lỗi
        return response()->json([
            'success' => false,
            'message' => 'Lỗi: Không tìm thấy file ảnh nào!'
        ], 400);
    }
}
