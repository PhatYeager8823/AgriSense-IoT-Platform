<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AIWebhookController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\ImageUploadController; // Nhớ dòng này
use App\Http\Controllers\DetectionController; // Nhớ đổi đúng tên Controller của bạn

// --- ĐOẠN MẶC ĐỊNH (Cứ để đây, sau này lỡ cần dùng API login thì tính sau) ---
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// --- ĐOẠN QUAN TRỌNG CỦA BẠN (Nằm riêng bên ngoài) ---
// Python sẽ bắn vào đây. Không cần đăng nhập (Public) để demo cho dễ.
Route::post('/ai-detect', [AIWebhookController::class, 'store']);
Route::post('/sensors/store', [SensorController::class, 'store']);
// API chuyên để nhận file ảnh từ Python/ESP32
Route::post('/upload', [ImageUploadController::class, 'upload']);
// Đường dẫn dành riêng cho Python/IoT (Không cần CSRF Token)
Route::post('/upload-image', [DetectionController::class, 'upload']);
