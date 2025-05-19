<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
}
