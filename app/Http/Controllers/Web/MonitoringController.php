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
    // File: app/Http/Controllers/Web/MonitoringController.php

    public function iot()
    {
        // 1. Lấy danh sách lịch sử (Phân trang)
        $sensorData = SensorLog::orderBy('recorded_at', 'desc')->paginate(20);

        // 2. Lấy dòng mới nhất để hiển thị trạng thái thiết bị
        $latestItem = SensorLog::orderBy('recorded_at', 'desc')->first();

        // 🔥 XỬ LÝ NULL CHO TRANG IOT
        $currentStatus = [
            'temperature'   => $latestItem ? $latestItem->temperature : 0,
            'humidity'      => $latestItem ? $latestItem->humidity : 0,
            'soil_moisture' => $latestItem ? $latestItem->soil_moisture : 0,
            'pump_status'   => $latestItem ? $latestItem->pump_status : 0,
            'fan_status'    => $latestItem ? $latestItem->fan_status : 0,
            'heater_status' => $latestItem ? $latestItem->heater_status : 0,
        ];

        $latestDetection = DiseaseDetection::latest('detected_at')->first();

        return view('monitoring.iot', compact('sensorData', 'latestDetection', 'currentStatus'));
    }
}
