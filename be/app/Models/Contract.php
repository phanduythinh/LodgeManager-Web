<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'MaHopDong';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaHopDong',
        'NguoiThue',
        'NgayBatDau',
        'NgayKetThuc',
        'TienCoc',
        'TienPhong',
        'SoPhong',
        'SoNha',
        'MaChuTro',
        'MaNhaTro',
        'MaPhong'
    ];

    protected $casts = [
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'TienCoc' => 'float',
        'TienPhong' => 'float'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'MaNhaTro', 'MaNhaTro');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'MaPhong', 'MaPhong');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'MaHopDong', 'MaHopDong');
    }
}
