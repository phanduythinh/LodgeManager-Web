<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocument extends Model
{
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'MaKhachHang', 'MaKhachHang');
    }
}
