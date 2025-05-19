<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    use HasFactory;

    protected $table = 'owners';
    protected $primaryKey = 'MaChuTro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'MaChuTro',
        'Ten',
        'SDT',
        'Email',
        'DiaChi'
    ];

    // Validation rules
    public static $rules = [
        'MaChuTro' => 'required|string|max:30|unique:owners',
        'Ten' => 'required|string|max:50',
        'SDT' => 'required|string|max:50',
        'Email' => 'required|email|max:50|unique:owners',
        'DiaChi' => 'required|string|max:50'
    ];

    // Relationships
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'MaChuTro', 'MaChuTro');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'MaChuTro', 'MaChuTro');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'MaChuTro', 'MaChuTro');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'MaChuTro', 'MaChuTro');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaChuTro', 'MaChuTro');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'MaChuTro', 'MaChuTro');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'MaChuTro', 'MaChuTro');
    }
}
