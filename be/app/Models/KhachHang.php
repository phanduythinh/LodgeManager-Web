<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KhachHang extends Model
{
    protected $table = 'khach_hangs';

    protected $fillable = [
        'ma_khach_hang',
        'ho_ten',
        'so_dien_thoai',
        'email',
        'cccd',
        'gioi_tinh',
        'ngay_sinh',
        'dia_chi_nha',
        'xa_phuong',
        'quan_huyen',
        'tinh_thanh'
    ];

    protected $casts = [
        'ngay_sinh' => 'date'
    ];

    public function hopDongs(): BelongsToMany
    {
        return $this->belongsToMany(HopDong::class, 'hop_dong_khach_hang')
            ->withTimestamps();
    }
}
