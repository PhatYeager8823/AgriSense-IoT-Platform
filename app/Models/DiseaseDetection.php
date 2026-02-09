<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseDetection extends Model
{
    use HasFactory;

    // 1. Khai báo tên bảng (Không bắt buộc nếu bạn đặt đúng chuẩn, nhưng nên có cho chắc)
    protected $table = 'disease_detections';

    // 2. KHAI BÁO CÁC CỘT ĐƯỢC PHÉP GHI DỮ LIỆU (QUAN TRỌNG NHẤT)
    // Nếu thiếu cái này, lệnh create() trong Controller sẽ bị chặn ngay lập tức.
    protected $fillable = [
        'farm_id',
        'image_url',
        'disease_name',
        'confidence',
        'temperature',
        'humidity',
        'detected_at'
    ];

    // HÀM DỊCH TÊN BỆNH SANG TIẾNG VIỆT (ACCESSOR)
    // Cách dùng trong View: $item->disease_name_vi
    public function getDiseaseNameViAttribute()
    {
        $map = [
            'Bacterial Spot'         => 'Bệnh Đốm Vi Khuẩn',
            'Early Blight'           => 'Nấm Đốm Vòng (Sớm)',
            'Healthy'                => 'Cây Khỏe Mạnh',
            'Iron Deficiency'        => 'Thiếu Sắt (Dinh dưỡng)',
            'Late Blight'            => 'Nấm Sương Mai (Muộn)',
            'Leaf Mold'              => 'Nấm Mốc Lá',
            'Leaf_Miner'             => 'Sâu Vẽ Bùa',
            'Mosaic Virus'           => 'Virus Khảm Lá',
            'Septoria'               => 'Đốm Lá Septoria',
            'Spider Mites'           => 'Nhện Đỏ',
            'Yellow Leaf Curl Virus' => 'Virus Xoăn Vàng Lá',
        ];

        // Nếu tìm thấy trong từ điển thì trả về tiếng Việt,
        // không thấy thì trả về tên gốc tiếng Anh
        return $map[$this->disease_name] ?? $this->disease_name;
    }

    // 3. Ép kiểu dữ liệu (Tùy chọn, để code sạch hơn)
    protected $casts = [
        'detected_at' => 'datetime',
        'confidence' => 'float',
    ];

    // 4. Mối quan hệ với bảng Farms
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
