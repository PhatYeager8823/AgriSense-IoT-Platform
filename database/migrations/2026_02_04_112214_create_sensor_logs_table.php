<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id')->default(1);

            // Các chỉ số môi trường
            $table->float('temperature'); // Nhiệt độ
            $table->float('humidity');    // Độ ẩm không khí
            $table->float('soil_moisture')->nullable(); // Độ ẩm đất

            // --- THÊM CÁC CỘT TRẠNG THÁI THIẾT BỊ ---
            // Dùng kiểu boolean (0: Tắt, 1: Bật) và mặc định là false (0)
            $table->boolean('pump_status')->default(false);   // Trạng thái Bơm
            $table->boolean('fan_status')->default(false);    // Trạng thái Quạt
            $table->boolean('heater_status')->default(false); // Trạng thái Đèn sưởi

            $table->timestamp('recorded_at'); // Thời gian ghi nhận
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
