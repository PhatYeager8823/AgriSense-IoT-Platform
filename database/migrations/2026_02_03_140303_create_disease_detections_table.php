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
        Schema::create('disease_detections', function (Blueprint $table) {
            $table->id();
            // Liên kết với bảng farms -> Biết bệnh này của nhà ai (Community Cloud)
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');

            $table->string('image_url'); // Link ảnh trên S3 (Quan trọng nhất)
            $table->string('disease_name'); // Tên bệnh (VD: Early Blight)
            $table->float('confidence'); // Độ tin cậy (VD: 0.85)

            // Lưu thêm thông số môi trường lúc phát hiện (Smart Farming)
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();

            $table->timestamp('detected_at'); // Thời gian phát hiện
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_detections');
    }
};
