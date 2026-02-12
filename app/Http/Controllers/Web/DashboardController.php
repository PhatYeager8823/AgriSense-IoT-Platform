<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiseaseDetection;
use App\Models\SensorLog; // <--- Nhớ import Model bảng mới

class DashboardController extends Controller
{
    public function index()
    {
        // 1. LẤY DỮ LIỆU CẢM BIẾN
        $sensorData = SensorLog::orderBy('recorded_at', 'desc')->take(24)->get();

        // Lấy dòng mới nhất (Có thể bị null nếu DB trống)
        $latestSensor = $sensorData->first();

        // 🔥 SỬA LỖI MA TRƠI (GHOST STATE) TẠI ĐÂY:
        // Tạo một biến chuẩn hóa, nếu $latestSensor là null thì gán bằng 0 hết
        $currentStatus = [
            'temperature'   => $latestSensor ? $latestSensor->temperature : 0,
            'humidity'      => $latestSensor ? $latestSensor->humidity : 0,
            'soil_moisture' => $latestSensor ? $latestSensor->soil_moisture : 0,

            // Quan trọng nhất: Thiết bị phải là 0 (TẮT)
            'pump_status'   => $latestSensor ? $latestSensor->pump_status : 0,
            'fan_status'    => $latestSensor ? $latestSensor->fan_status : 0,
            'heater_status' => $latestSensor ? $latestSensor->heater_status : 0,

            'recorded_at'   => $latestSensor ? $latestSensor->recorded_at : null,
        ];

        // 2. LẤY DỮ LIỆU BỆNH
        $detections = DiseaseDetection::all();

        // 3. Trả về View (Lưu ý: mình truyền thêm biến $currentStatus)
        return view('dashboard.index', compact('sensorData', 'latestSensor', 'detections', 'currentStatus'));
    }
}
