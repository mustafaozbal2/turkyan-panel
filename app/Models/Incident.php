<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

   protected $fillable = [
    'name',
    'location',
    'severity',
    'area_hectares',
    'response_time_minutes',
    'latitude',              // 🟢 EKLENDİ
    'longitude',             // 🟢 EKLENDİ
];

}