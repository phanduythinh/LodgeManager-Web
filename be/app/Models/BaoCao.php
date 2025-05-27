<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCao extends Model
{
    use HasFactory;

    protected $table = 'bao_caos';

    protected $fillable = [
        'ten',
        'loai',
        'ngay_tao',
        'ngay_cap_nhat',
        'noi_dung',
        'file_path',
        'toa_nha_id',
        'nguoi_tao_id',
        'trang_thai'
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
        'ngay_cap_nhat' => 'datetime'
    ];

    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class);
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }
}
