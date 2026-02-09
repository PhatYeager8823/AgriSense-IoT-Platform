<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'temperature',
        'humidity',
        'soil_moisture',
        'recorded_at',
        'pump_status',
        'fan_status',
        'heater_status'
        ];

    // Khai báo là kiểu ngày tháng để lát nữa format cho dễ
    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
