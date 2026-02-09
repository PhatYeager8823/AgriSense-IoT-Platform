<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // 1. Tạo Farm trước (Phải có Farm mới có Sensor)
        $this->call(FarmSeeder::class);

        // 2. Sau đó mới tạo dữ liệu cảm biến
        $this->call(SensorSeeder::class);

        // 3. Tạo User mẫu để đăng nhập (nếu cần)
        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('123456'),
        // ]);
    }
}
