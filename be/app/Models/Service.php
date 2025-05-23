<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Service",
 *     title="Service",
 *     description="Service model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Internet"),
 *     @OA\Property(property="description", type="string", nullable=true, example="High-speed internet service"),
 *     @OA\Property(property="price", type="number", format="float", example=200000),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive"},
 *         example="active"
 *     ),
 *     @OA\Property(property="note", type="string", nullable=true, example="Service note"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services';
    protected $primaryKey = 'MaDichVu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'note'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    // Validation rules
    public static $rules = [
        'MaDichVu' => 'required|string|max:30|unique:services',
        'TenDichVu' => 'required|string|max:50',
        'DonViTinh' => 'required|string|max:10',
        'DonGia' => 'required|numeric|min:0',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(Contract::class, 'contract_services')
            ->withPivot(['quantity', 'price', 'start_date', 'end_date'])
            ->withTimestamps();
    }
}
