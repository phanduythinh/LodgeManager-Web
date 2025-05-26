<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phong extends Model
{
    protected $table = 'phongs';

    protected $fillable = [
        'toa_nha_id',
        'ma_phong',
        'ten_phong',
        'tang',
        'gia_thue',
        'dat_coc',
        'dien_tich',
        'so_khach_toi_da',
        'trang_thai'
    ];

    public function toaNha(): BelongsTo
    {
        return $this->belongsTo(ToaNha::class);
    }

    public function hopDongs(): HasMany
    {
        return $this->hasMany(HopDong::class);
    }
}
