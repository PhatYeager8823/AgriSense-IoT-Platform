<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiseaseDetection;
use App\Models\SensorLog;

class MonitoringController extends Controller
{
    // Trang Camera AI: Chỉ quan tâm bảng DiseaseDetection
    public function camera()
    {
        $detections = DiseaseDetection::orderBy('detected_at', 'desc')->paginate(20);
        return view('monitoring.camera', compact('detections'));
    }

    // Trang IoT: Lấy dữ liệu từ SensorLog
    public function iot()
    {
        // 1. Lấy dữ liệu cảm biến từ bảng RIÊNG (SensorLog)
        $sensorData = SensorLog::orderBy('recorded_at', 'desc')->paginate(20);

        // 2. Vẫn lấy tin cảnh báo AI mới nhất để hiển thị lời khuyên
        $latestDetection = DiseaseDetection::latest('detected_at')->first();

        return view('monitoring.iot', compact('sensorData', 'latestDetection'));
    }
}
