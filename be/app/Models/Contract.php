<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

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

    // Validation rules
    public static $rules = [
        'MaHopDong' => 'required|string|max:30|unique:contracts',
        'NguoiThue' => 'required|string|max:30',
        'NgayBatDau' => 'required|date',
        'NgayKetThuc' => 'required|date|after:NgayBatDau',
        'TienCoc' => 'required|numeric|min:0',
        'TienPhong' => 'required|numeric|min:0',
        'SoPhong' => 'required|string|max:50',
        'SoNha' => 'required|string|max:50',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro',
        'MaNhaTro' => 'required|string|exists:buildings,MaNhaTro',
        'MaPhong' => 'required|string|exists:rooms,MaPhong'
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

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'MaPhong', 'MaPhong');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'MaHopDong', 'MaHopDong');
    }
}
