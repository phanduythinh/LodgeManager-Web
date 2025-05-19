<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'MaHoaDon';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaHoaDon',
        'TenKhach',
        'SoPhong',
        'NgayDongTien',
        'TongSoTien',
        'PhuongThucThanhToan',
        'ChiTiet',
        'TrangThai',
        'MaChuTro',
        'MaBaoCao',
        'MaKhachHang'
    ];

    protected $casts = [
        'NgayDongTien' => 'date',
        'TongSoTien' => 'float'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'MaBaoCao', 'MaBaoCao');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'MaKhachHang', 'MaKhachHang');
    }
}
