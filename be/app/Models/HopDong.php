<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HopDong extends Model
{
    protected $table = 'hop_dongs';

    protected $fillable = [
        'ma_hop_dong',
        'phong_id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'tien_thue',
        'tien_coc',
        'chu_ky_thanh_toan',
        'ngay_tinh_tien',
        'trang_thai'
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngay_tinh_tien' => 'date'
    ];

    public function phong(): BelongsTo
    {
        return $this->belongsTo(Phong::class);
    }

    public function khachHangs(): BelongsToMany
    {
        return $this->belongsToMany(KhachHang::class, 'hop_dong_khach_hang')
            ->withTimestamps();
    }

    public function phiDichVus(): BelongsToMany
    {
        return $this->belongsToMany(PhiDichVu::class, 'hop_dong_dich_vu')
            ->withPivot(['ma_cong_to', 'chi_so_dau', 'ngay_tinh_phi'])
            ->withTimestamps();
    }
}
