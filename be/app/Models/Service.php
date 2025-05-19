<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

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

    // Validation rules
    public static $rules = [
        'MaDichVu' => 'required|string|max:30|unique:services',
        'TenDichVu' => 'required|string|max:50',
        'DonViTinh' => 'required|string|max:10',
        'DonGia' => 'required|numeric|min:0',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }
}
