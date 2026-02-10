<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DetectionController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            // 1. Lưu ảnh
            $path = $request->file('image')->store('uploads', 'public');

            // 2. Lấy tên bệnh gửi kèm (nếu có)
            $disease = $request->input('disease_name', 'Không rõ');
            $conf = $request->input('confidence', 0);

            // TODO: Tại đây bạn có thể viết code lưu vào bảng 'detections' trong Database
            // Detection::create([...]);

            return response()->json([
                'success' => true,
                'url' => url("storage/$path"),
                'note' => "Đã nhận ảnh bệnh: $disease"
            ]);
        }
        return response()->json(['error' => 'No file'], 400);
    }
}
