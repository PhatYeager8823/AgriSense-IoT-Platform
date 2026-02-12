<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MonitoringController;
use App\Http\Controllers\ChatbotController;

// 1. Dashboard Tổng (Chỉ hiện biểu đồ)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 2. Trang Camera AI (Xem ảnh và log bệnh)
Route::get('/camera-ai', [MonitoringController::class, 'camera'])->name('camera.index');

// 3. Trang Nông trại IoT (Xem cảm biến)
Route::get('/farm-iot', [MonitoringController::class, 'iot'])->name('iot.index');

// 4. Trang giao diện tư vấn riêng
Route::get('/ai-consultant', [ChatbotController::class, 'index'])->name('ai.consultant');

// 5. Route cho Chatbot
Route::post('/ask-ai', [ChatbotController::class, 'askGemini'])->name('ask.ai');

// 6. Route xóa lịch sử chat
Route::post('/clear-chat', [ChatbotController::class, 'clearHistory'])->name('clear.chat');

// --- MẸO: Route đặc biệt để chạy lệnh mà không cần Shell ---
// --- FILE: routes/web.php ---

Route::get('/fix-image', function () {
    // $link = public_path('storage'); // Bỏ dòng này

    echo "<h1>🛠️ KIỂM TRA ẢNH TRONG KHO</h1>";

    // 1. BỎ QUA BƯỚC XÓA LINK (Vì đã có sẵn và không xóa được)
    // if (file_exists($link)) { unlink($link); } <--- XÓA DÒNG NÀY ĐI

    // 2. BỎ QUA BƯỚC TẠO LINK (Vì Docker đã tự tạo lúc khởi động rồi)

    // 3. CHỈ CẦN KIỂM TRA FILE THÔI
    $path = storage_path('app/public/uploads');

    if (!is_dir($path)) {
        echo "<h3 style='color:red'>❌ Thư mục uploads chưa được tạo!</h3>";
        // Thử tạo thư mục nếu chưa có
        mkdir($path, 0775, true);
        echo "<p>Đã thử tạo thư mục mới...</p>";
    } else {
        echo "<h3 style='color:green'>✅ Thư mục uploads ĐÃ TỒN TẠI.</h3>";
    }

    // Liệt kê file
    $files = glob($path . '/*');
    echo "<h3>📂 Danh sách file ảnh hiện có:</h3>";

    if (count($files) > 0) {
        echo "<ul>";
        foreach ($files as $file) {
            $filename = basename($file);
            $url = asset('storage/uploads/' . $filename);
            echo "<li>";
            echo "<strong>File:</strong> $filename <br>";
            echo "<strong>Link xem thử:</strong> <a href='$url' target='_blank'>$url</a>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>⚠️ Kho đang trống! (Do Render Free tự xóa hoặc Python chưa gửi lên)</p>";
        echo "<p>👉 Hãy chạy lại file Python để gửi ảnh mới ngay lập tức!</p>";
    }
});

// Đường dẫn tạo Farm chuẩn theo DB của bạn
Route::get('/seed-farm-final', function () {
    try {
        // 1. Kiểm tra xem Farm số 1 có chưa
        $exists = DB::table('farms')->where('id', 1)->exists();

        if ($exists) {
            return "<h1 style='color:orange'>⚠️ Farm số 1 đã có rồi! Không cần tạo lại.</h1>";
        }

        // 2. Tạo mới với đúng tên cột trong Database của bạn
        DB::table('farms')->insert([
            'id' => 1,
            'name' => 'Hợp tác xã Dola Pharmacy',   // Tên Farm
            'owner_name' => 'Admin',                // 🔥 BẮT BUỘC PHẢI CÓ
            'location' => 'Bạc Liêu, Việt Nam',     // Cột này tên là location
            'crop_type' => 'Cà chua',               // Loại cây
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return "<h1 style='color:green'>✅ ĐÃ TẠO THÀNH CÔNG FARM SỐ 1!</h1>";

    } catch (\Exception $e) {
        // Nếu lỗi, in chi tiết ra để sửa
        return "<h1 style='color:red'>❌ Lỗi: " . $e->getMessage() . "</h1>";
    }
});

// Link Reset toàn bộ hệ thống để Demo
Route::get('/reset-all', function () {
    try {
        // 1. Xóa cảm biến
        DB::table('sensor_logs')->delete();

        // 2. Xóa lịch sử bệnh (Ảnh AI)
        DB::table('disease_detections')->delete();

        // 3. Xóa lịch sử Chatbot
        DB::table('chat_histories')->delete();

        return "<div style='text-align:center; font-family:sans-serif; padding-top:50px;'>
                    <h1 style='color:green; font-size:40px;'>✨ HỆ THỐNG ĐÃ SẠCH SẼ! ✨</h1>
                    <h3>Sẵn sàng để Demo.</h3>
                    <p>1. Bật Python <b>simulate_sensors.py</b> (Gửi cảm biến)</p>
                    <p>2. Bật Python <b>detector.py</b> (Gửi ảnh bệnh)</p>
                    <p>3. F5 trang Dashboard và lượm điểm 10! 🏆</p>
                </div>";
    } catch (\Exception $e) {
        return "<h1 style='color:red'>❌ Lỗi: " . $e->getMessage() . "</h1>";
    }
});
