<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerReport extends Model
{
    use HasFactory;

   protected $fillable = [
    'user_id',
    'latitude',
    'longitude',
    'description',
    'image_path',
    'status',
    'assigned_user_id', // 🟢 BUNU EKLE
];


    /**
     * Bu ihbarı gönderen kullanıcıyı döndürür.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
