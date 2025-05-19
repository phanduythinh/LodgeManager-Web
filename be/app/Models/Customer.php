<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'MaKhachHang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaKhachHang',
        'TenKhachHang',
        'SDT',
        'Email',
        'DiaChiThuongTru',
        'NgaySinh',
        'CCCD',
        'MaChuTro'
    ];

    protected $casts = [
        'NgaySinh' => 'date'
    ];

    // Validation rules
    public static $rules = [
        'MaKhachHang' => 'required|string|max:10|unique:customers',
        'TenKhachHang' => 'required|string|max:30',
        'SDT' => 'required|string|max:10',
        'Email' => 'required|email|max:50|unique:customers',
        'DiaChiThuongTru' => 'required|string|max:50',
        'NgaySinh' => 'required|date',
        'CCCD' => 'required|string|max:50',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class, 'MaKhachHang', 'MaKhachHang');
    }
}
