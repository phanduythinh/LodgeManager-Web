<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiayTo extends Model
{
    use HasFactory;

    protected $table = 'giay_tos';

    protected $fillable = [
        'ten',
        'loai',
        'ngay_cap',
        'noi_cap',
        'ngay_het_han',
        'file_path',
        'toa_nha_id',
        'ghi_chu',
        'trang_thai'
    ];

    protected $casts = [
        'ngay_cap' => 'date',
        'ngay_het_han' => 'date'
    ];

    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class);
    }
}
