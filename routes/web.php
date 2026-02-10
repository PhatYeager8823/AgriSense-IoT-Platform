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
Route::get('/setup-render', function() {
    try {
        echo "<h1>BẮT ĐẦU CÀI ĐẶT...</h1>";

        // 1. Chạy Migration (Tạo bảng Database)
        \Artisan::call('migrate --force');
        echo "<h3 style='color:green'>1. Migration (Tạo bảng): OK</h3>";
        echo "<pre>" . \Artisan::output() . "</pre>";

        // 2. Tạo shortcut cho ảnh
        \Artisan::call('storage:link');
        echo "<h3 style='color:green'>2. Storage Link (Sửa lỗi ảnh): OK</h3>";
        echo "<pre>" . \Artisan::output() . "</pre>";

        // 3. Xóa cache cũ
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        echo "<h3 style='color:green'>3. Clear Cache: OK</h3>";

        echo "<h1 style='color:blue'>✅ CÀI ĐẶT THÀNH CÔNG! HÃY VỀ TRANG CHỦ.</h1>";

    } catch (\Exception $e) {
        echo "<h1 style='color:red'>❌ LỖI RỒI:</h1>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
});
