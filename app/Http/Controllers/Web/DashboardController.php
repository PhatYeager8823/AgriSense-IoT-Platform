<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiseaseDetection;
use App\Models\SensorLog;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. LẤY DỮ LIỆU
        $sensorData = SensorLog::orderBy('recorded_at', 'desc')->take(24)->get();
        $latestSensor = $sensorData->first();

        // 2. TẠO BIẾN CHUẨN HÓA (ĐOẠN NÀY QUAN TRỌNG NHẤT)
        // Nếu không có dữ liệu ($latestSensor = null), ta ép nó bằng 0 hết.
        $currentStatus = [
            'temperature'   => $latestSensor ? $latestSensor->temperature : 0,
            'humidity'      => $latestSensor ? $latestSensor->humidity : 0,
            'soil_moisture' => $latestSensor ? $latestSensor->soil_moisture : 0,
            'pump_status'   => $latestSensor ? $latestSensor->pump_status : 0,
            'fan_status'    => $latestSensor ? $latestSensor->fan_status : 0,
            'heater_status' => $latestSensor ? $latestSensor->heater_status : 0,
        ];

        // 3. LẤY DỮ LIỆU BỆNH
        $detections = DiseaseDetection::all();

        // 4. TRUYỀN $currentStatus SANG VIEW
        return view('dashboard.index', compact('sensorData', 'latestSensor', 'detections', 'currentStatus'));
    }
}
