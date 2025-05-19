<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'MaPhong';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaPhong',
        'DienTich',
        'GiaThue',
        'CoSoVatChat',
        'SoNguoiToiDa',
        'NgayHetHanHopDong',
        'TrangThai',
        'MaChuTro',
        'MaNhaTro',
        'MaHopDong'
    ];

    protected $casts = [
        'NgayHetHanHopDong' => 'date',
        'DienTich' => 'float',
        'GiaThue' => 'float'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'MaNhaTro', 'MaNhaTro');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'MaHopDong', 'MaHopDong');
    }
}
