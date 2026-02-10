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
    $target = storage_path('app/public');
    $link = public_path('storage');

    echo "<h1>🛠️ CÔNG CỤ SỬA LỖI ẢNH</h1>";

    // 1. Xóa link cũ nếu có (để tạo lại cho sạch)
    if (file_exists($link)) {
        unlink($link);
        echo "<p style='color:orange'>Da xoa link cu...</p>";
    }

    // 2. Chạy lệnh storage:link bằng code
    try {
        symlink($target, $link);
        echo "<h3 style='color:green'>✅ Đã tạo Symlink thành công!</h3>";
    } catch (\Exception $e) {
        echo "<h3 style='color:red'>❌ Lỗi: " . $e->getMessage() . "</h3>";
    }

    // 3. Kiểm tra xem trong ổ cứng có ảnh nào không
    $files = glob(storage_path('app/public/uploads/*'));
    echo "<h3>📂 Danh sách file trong kho (Storage):</h3>";
    if (count($files) > 0) {
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li>" . basename($file) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>⚠️ Kho đang trống! Chưa có ảnh nào được gửi lên.</p>";
    }
});
