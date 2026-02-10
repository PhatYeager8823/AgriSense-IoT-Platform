<?php

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

// Đường dẫn đặc biệt để tạo dữ liệu Farm số 1
Route::get('/seed-farm-fix', function () {
    try {
        // Kiểm tra xem đã có Farm số 1 chưa
        $exists = DB::table('farms')->where('id', 1)->exists();

        if ($exists) {
            return "<h1 style='color:orange'>⚠️ Farm ID=1 đã tồn tại rồi! Không cần tạo lại.</h1>";
        }

        // Nếu chưa có thì tạo mới
        // (Lưu ý: Bạn kiểm tra xem bảng 'farms' trong DB của bạn tên cột là 'address' hay 'location' nhé)
        DB::table('farms')->insert([
            'id' => 1,
            'name' => 'Farm Demo AgriSense',
            'address' => 'HCMC, Vietnam', // Nếu lỗi cột 'address', hãy đổi thành 'location'
            // 'user_id' => 1,            // Bỏ dấu // ở đầu dòng này nếu bảng farms yêu cầu người dùng
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return "<h1 style='color:green'>✅ ĐÃ TẠO THÀNH CÔNG FARM SỐ 1!</h1>";

    } catch (\Exception $e) {
        return "<h1 style='color:red'>❌ Lỗi: " . $e->getMessage() . "</h1>";
    }
});
