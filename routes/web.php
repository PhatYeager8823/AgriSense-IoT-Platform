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
