<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietHoaDon extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_hoa_dons';

    protected $fillable = [
        'hoa_don_id',
        'phi_dich_vu_id',
        'chi_so_cu',
        'chi_so_moi',
        'so_luong',
        'don_gia',
        'thanh_tien',
    ];

    public function hoaDon(): BelongsTo
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don_id');
    }

    public function phiDichVu(): BelongsTo
    {
        return $this->belongsTo(PhiDichVu::class, 'phi_dich_vu_id');
    }
}
