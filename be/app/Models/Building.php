<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';
    protected $primaryKey = 'MaNhaTro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaNhaTro',
        'TenNha',
        'DiaChi',
        'TongSoPhong',
        'SoPhongTrong',
        'MaChuTro'
    ];

    protected $casts = [
        'TongSoPhong' => 'integer',
        'SoPhongTrong' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'MaNhaTro' => 'required|string|max:30|unique:buildings',
        'TenNha' => 'required|string|max:50',
        'DiaChi' => 'required|string|max:50',
        'TongSoPhong' => 'required|integer|min:0',
        'SoPhongTrong' => 'required|integer|min:0|lte:TongSoPhong',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'MaNhaTro', 'MaNhaTro');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'MaNhaTro', 'MaNhaTro');
    }
}
