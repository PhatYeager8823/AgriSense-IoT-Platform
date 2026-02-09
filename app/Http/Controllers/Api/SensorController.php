<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorLog;
use Carbon\Carbon;

class SensorController extends Controller
{
    // File: app/Http/Controllers/Api/SensorController.php

    public function store(Request $request)
    {
        try {
            // 1. Validate dữ liệu đầu vào
            $data = $request->validate([
                'farm_id'       => 'required|integer',
                'temperature'   => 'required|numeric',
                'humidity'      => 'required|numeric',
                'soil_moisture' => 'nullable|numeric',

                // QUAN TRỌNG: Phải cho phép nhận 3 biến này
                'pump_status'   => 'nullable|boolean', // Hoặc numeric đều được
                'fan_status'    => 'nullable|boolean',
                'heater_status' => 'nullable|boolean',
            ]);

            // 2. Lưu vào Database
            $log = SensorLog::create([
                'farm_id'       => $data['farm_id'],
                'temperature'   => $data['temperature'],
                'humidity'      => $data['humidity'],
                'soil_moisture' => $data['soil_moisture'] ?? 0,

                // --- BỔ SUNG ĐOẠN NÀY ĐỂ LƯU TRẠNG THÁI ---
                'pump_status'   => $data['pump_status'] ?? 0,   // Nếu không gửi lên thì mặc định là 0
                'fan_status'    => $data['fan_status'] ?? 0,
                'heater_status' => $data['heater_status'] ?? 0,
                // ------------------------------------------

                'recorded_at'   => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Data received successfully',
                'data' => $log
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
