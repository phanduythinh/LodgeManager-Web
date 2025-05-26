<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PhiDichVu extends Model
{
    protected $table = 'phi_dich_vus';

    protected $fillable = [
        'toa_nha_id',
        'ma_dich_vu',
        'ten_dich_vu',
        'loai_dich_vu',
        'don_gia',
        'don_vi_tinh'
    ];

    public function toaNha(): BelongsTo
    {
        return $this->belongsTo(ToaNha::class);
    }

    public function hopDongs(): BelongsToMany
    {
        return $this->belongsToMany(HopDong::class, 'hop_dong_dich_vu')
            ->withPivot(['ma_cong_to', 'chi_so_dau', 'ngay_tinh_phi'])
            ->withTimestamps();
    }
}
