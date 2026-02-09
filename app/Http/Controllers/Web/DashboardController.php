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
        // 1. LẤY DỮ LIỆU CẢM BIẾN (Từ bảng sensor_logs)
        // Lấy 24 dòng mới nhất (tương đương 12 tiếng nếu 30p/lần) để vẽ biểu đồ Đường
        $sensorData = SensorLog::orderBy('recorded_at', 'desc')->take(24)->get();

        // Lấy thông số mới nhất để hiển thị lên Thẻ (Card)
        $latestSensor = $sensorData->first();

        // 2. LẤY DỮ LIỆU BỆNH (Từ bảng disease_detections)
        // Lấy tất cả để đếm số lượng và vẽ biểu đồ Tròn
        $detections = DiseaseDetection::all();

        // 3. Trả cả 2 biến về cho View
        return view('dashboard.index', compact('sensorData', 'latestSensor', 'detections'));
    }
}
