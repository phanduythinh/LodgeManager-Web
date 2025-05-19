<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocument extends Model
{
    use HasFactory;

    protected $table = 'legal_documents';
    protected $primaryKey = 'MaGiayTo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaGiayTo',
        'MaKhachHang',
        'TenKhachHang',
        'QueQuan',
        'TinhTrang'
    ];

    // Validation rules
    public static $rules = [
        'MaGiayTo' => 'required|string|max:30|unique:legal_documents',
        'MaKhachHang' => 'required|string|exists:customers,MaKhachHang',
        'TenKhachHang' => 'required|string|max:30',
        'QueQuan' => 'required|string|max:50',
        'TinhTrang' => 'required|string|max:50'
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'MaKhachHang', 'MaKhachHang');
    }
}
