<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Building",
 *     title="Building",
 *     description="Building model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Sunshine Apartment"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="description", type="string", example="A modern apartment complex"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "maintenance"}, example="active"),
 *     @OA\Property(property="total_floors", type="integer", example=10),
 *     @OA\Property(property="total_rooms", type="integer", example=50),
 *     @OA\Property(property="year_built", type="integer", example=2020),
 *     @OA\Property(property="owner_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buildings';
    protected $primaryKey = 'MaNhaTro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'address',
        'description',
        'status',
        'total_floors',
        'total_rooms',
        'year_built',
        'owner_id'
    ];

    protected $casts = [
        'total_floors' => 'integer',
        'total_rooms' => 'integer',
        'year_built' => 'integer',
        'owner_id' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'MaNhaTro' => 'required|string|max:30|unique:buildings',
        'TenNha' => 'required|string|max:50',
        'DiaChi' => 'required|string|max:50',
        'TongSoPhong' => 'required|integer|min:0',
        'SoPhongTrong' => 'required|integer|min:0|lte:TongSoPhong',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'MaNhaTro', 'MaNhaTro');
    }
}
