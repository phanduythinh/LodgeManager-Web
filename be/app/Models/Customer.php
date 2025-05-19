<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'birth_date',
        'id_card',
        'owner_id'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
