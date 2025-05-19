<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
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
