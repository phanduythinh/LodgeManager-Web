<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToaNha extends Model
{
    protected $table = 'toa_nhas';

    protected $fillable = [
        'ma_nha',
        'ten_nha',
        'dia_chi_nha',
        'xa_phuong',
        'quan_huyen',
        'tinh_thanh',
        'trang_thai'
    ];

    public function phongs(): HasMany
    {
        return $this->hasMany(Phong::class);
    }

    public function phiDichVus(): HasMany
    {
        return $this->hasMany(PhiDichVu::class);
    }
}
