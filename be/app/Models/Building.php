<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $table = 'buildings';
    protected $primaryKey = 'MaNhaTro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaNhaTro',
        'TenNha',
        'DiaChi',
        'TongSoPhong',
        'SoPhongTrong',
        'MaChuTro'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'MaNhaTro', 'MaNhaTro');
    }
}
