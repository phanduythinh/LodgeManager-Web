<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Room",
 *     title="Room",
 *     description="Room model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="room_number", type="string", example="101"),
 *     @OA\Property(property="building_id", type="integer", example=1),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"available", "occupied", "maintenance"},
 *         example="available"
 *     ),
 *     @OA\Property(property="price", type="number", format="float", example=2000000),
 *     @OA\Property(property="area", type="number", format="float", example=25.5),
 *     @OA\Property(property="floor", type="integer", example=1),
 *     @OA\Property(property="description", type="string", nullable=true, example="Room with balcony"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rooms';
    protected $primaryKey = 'MaPhong';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'room_number',
        'building_id',
        'status',
        'price',
        'area',
        'floor',
        'description'
    ];

    protected $casts = [
        'price' => 'float',
        'area' => 'float',
        'floor' => 'integer',
        'building_id' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'room_number' => 'required|string|max:30|unique:rooms',
        'building_id' => 'required|integer|exists:buildings,MaNhaTro',
        'status' => 'required|string|in:available,occupied,maintenance',
        'price' => 'required|numeric|min:0',
        'area' => 'required|numeric|min:0',
        'floor' => 'required|integer|min:1',
        'description' => 'nullable|string|max:255'
    ];

    // Relationships
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaPhong', 'MaPhong');
    }
}
