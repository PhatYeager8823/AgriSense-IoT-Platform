<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\SensorLog;

class SensorSeeder extends Seeder
{
    public function run()
    {
        SensorLog::truncate(); // Xóa sạch dữ liệu cũ

        // Kịch bản 24h chuẩn khoa học (Theo bảng bạn cung cấp)
        // [Giờ, Nhiệt độ, Độ ẩm đất]
        $dataScenario = [
            [0,  17, 60], // 00h
            [1,  16, 59], // 01h
            [2,  15, 58], // 02h
            [3,  15, 57], // 03h
            [4,  16, 56], // 04h
            [5,  18, 55], // 05h
            [6,  20, 54], // 06h
            [7,  22, 53], // 07h
            [8,  24, 52], // 08h
            [9,  26, 50], // 09h
            [10, 29, 48], // 10h
            [11, 31, 46], // 11h - Nóng -> Bật Quạt
            [12, 33, 44], // 12h - Rất nóng, Đất khô -> Bật Bơm, Bật Quạt
            [13, 34, 60], // 13h - Đất ẩm lại (do vừa tưới)
            [14, 32, 58], // 14h
            [15, 30, 56], // 15h
            [16, 29, 54], // 16h
            [17, 27, 52], // 17h
            [18, 25, 50], // 18h
            [19, 23, 48], // 19h
            [20, 22, 46], // 20h
            [21, 21, 45], // 21h
            [22, 19, 44], // 22h - Đất khô -> Bật Bơm
            [23, 18, 60], // 23h
        ];

        // Lấy thời điểm hiện tại làm mốc 23h hôm nay
        $now = Carbon::now();

        foreach ($dataScenario as $data) {
            $hour = $data[0];
            $temp = $data[1];
            $soil = $data[2];

            // 1. Tính toán logic trạng thái thiết bị (Giống hệt Python)
            $pump = ($soil < 45) ? true : false;
            $fan  = ($temp >= 30) ? true : false;
            $heater = ($temp < 16) ? true : false;

            // 2. Tạo thời gian lùi về quá khứ
            // (Mẹo: dùng today() setHour để khớp giờ hiển thị đẹp hơn)
            $recordTime = Carbon::today()->setHour($hour)->setMinute(0);

            // 3. Insert vào DB
            SensorLog::create([
                'farm_id' => 1,
                'temperature' => $temp,
                'humidity' => 100 - $temp - rand(0, 5), // Công thức giả lập độ ẩm không khí
                'soil_moisture' => $soil,
                'pump_status' => $pump,
                'fan_status' => $fan,
                'heater_status' => $heater,
                'recorded_at' => $recordTime,
            ]);
        }
    }
}
