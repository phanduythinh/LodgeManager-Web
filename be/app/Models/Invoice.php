<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

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

    // Validation rules
    public static $rules = [
        'MaHoaDon' => 'required|string|max:30|unique:invoices',
        'TenKhach' => 'required|string|max:30',
        'SoPhong' => 'required|string|max:50',
        'NgayDongTien' => 'required|date',
        'TongSoTien' => 'required|numeric|min:0',
        'PhuongThucThanhToan' => 'required|string|in:Tiền mặt,Chuyển khoản',
        'ChiTiet' => 'required|string|max:50',
        'TrangThai' => 'required|string|in:Chưa thanh toán,Đã thanh toán',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro',
        'MaBaoCao' => 'nullable|string|exists:reports,MaBaoCao',
        'MaKhachHang' => 'required|string|exists:customers,MaKhachHang'
    ];

    // Relationships
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
