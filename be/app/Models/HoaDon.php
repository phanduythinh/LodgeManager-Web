<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model HoaDon (Invoice/Bill)
 * Bảng: hoa_dons
 */
class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoa_dons';

    protected $fillable = [
        'ma_hoa_don',
        'hop_dong_id',
        'ngay_tao',
        'ngay_thanh_toan',
        'tong_tien',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_thanh_toan' => 'date',
        'tong_tien' => 'float',
    ];

    public function hopDong(): BelongsTo
    {
        return $this->belongsTo(HopDong::class, 'hop_dong_id');
    }
}
