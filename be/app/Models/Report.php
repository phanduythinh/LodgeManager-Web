<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'MaBaoCao';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaBaoCao',
        'DoanhThu',
        'LoiNhuan',
        'SoNguoiThue',
        'SoNhaTro',
        'SoPhongTrong',
        'MaChuTro',
        'MaHopDong'
    ];

    protected $casts = [
        'DoanhThu' => 'float',
        'LoiNhuan' => 'float',
        'SoNguoiThue' => 'integer',
        'SoNhaTro' => 'integer',
        'SoPhongTrong' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'MaBaoCao' => 'required|string|max:30|unique:reports',
        'DoanhThu' => 'required|numeric|min:0',
        'LoiNhuan' => 'required|numeric|min:0',
        'SoNguoiThue' => 'required|integer|min:0',
        'SoNhaTro' => 'required|integer|min:0',
        'SoPhongTrong' => 'required|integer|min:0',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro',
        'MaHopDong' => 'nullable|string|exists:contracts,MaHopDong'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'MaHopDong', 'MaHopDong');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaBaoCao', 'MaBaoCao');
    }
}
