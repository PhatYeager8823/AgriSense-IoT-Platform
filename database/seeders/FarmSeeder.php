<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FarmSeeder extends Seeder
{
    public function run()
    {
        // Kiểm tra nếu chưa có ID=1 thì mới tạo
        if (DB::table('farms')->where('id', 1)->doesntExist()) {
            DB::table('farms')->insert([
                'id' => 1,
                'name' => 'Hợp tác xã Dola Pharmacy',
                'owner_name' => 'Nguyễn Văn A',
                'location' => 'Bạc Liêu, Việt Nam', // Sửa 'address' -> 'location'
                'crop_type' => 'Cà chua',           // Thêm cột này vào cho đủ
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
