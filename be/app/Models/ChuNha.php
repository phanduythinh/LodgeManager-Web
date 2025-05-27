<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuNha extends Model
{
    use HasFactory;

    protected $table = 'chu_nhas';

    protected $fillable = [
        'ho_ten',
        'so_dien_thoai',
        'email',
        'dia_chi',
        'cmnd',
        'ngay_cap_cmnd',
        'noi_cap_cmnd',
        'ngay_sinh',
        'gioi_tinh',
        'trang_thai'
    ];

    protected $casts = [
        'ngay_cap_cmnd' => 'date',
        'ngay_sinh' => 'date'
    ];

    public function toaNhas()
    {
        return $this->hasMany(ToaNha::class);
    }
}
