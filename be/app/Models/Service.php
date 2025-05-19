<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'MaDichVu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaDichVu',
        'TenDichVu',
        'DonViTinh',
        'DonGia',
        'MaChuTro'
    ];

    protected $casts = [
        'DonGia' => 'float'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }
} 