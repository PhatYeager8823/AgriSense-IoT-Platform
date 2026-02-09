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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nông trại (VD: Nông trại A - Bạc Liêu)
            $table->string('owner_name'); // Tên chủ hộ (Ông Ba, Bà Tư...)
            $table->string('location')->nullable(); // Vị trí (để hiển thị bản đồ nếu cần)
            $table->string('crop_type')->default('Tomato'); // Loại cây trồng chính
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
