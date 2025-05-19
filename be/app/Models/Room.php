<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

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
        'GiaThue' => 'float',
        'SoNguoiToiDa' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'MaPhong' => 'required|string|max:30|unique:rooms',
        'DienTich' => 'required|numeric|min:0',
        'GiaThue' => 'required|numeric|min:0',
        'CoSoVatChat' => 'required|string|max:50',
        'SoNguoiToiDa' => 'required|integer|min:1',
        'NgayHetHanHopDong' => 'nullable|date|after:today',
        'TrangThai' => 'required|string|in:Trống,Đã cho thuê,Đang sửa chữa',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro',
        'MaNhaTro' => 'required|string|exists:buildings,MaNhaTro',
        'MaHopDong' => 'nullable|string|exists:contracts,MaHopDong'
    ];

    // Relationships
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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaPhong', 'MaPhong');
    }
}
