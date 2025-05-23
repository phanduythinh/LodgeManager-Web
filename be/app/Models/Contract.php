<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Contract",
 *     title="Contract",
 *     description="Contract model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="room_id", type="integer", example=1),
 *     @OA\Property(property="customer_id", type="integer", example=1),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "expired", "terminated"},
 *         example="active"
 *     ),
 *     @OA\Property(property="deposit_amount", type="number", format="float", example=2000000),
 *     @OA\Property(property="monthly_rent", type="number", format="float", example=3000000),
 *     @OA\Property(property="payment_day", type="integer", example=5),
 *     @OA\Property(property="description", type="string", nullable=true, example="Contract for room 101"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts';
    protected $primaryKey = 'MaHopDong';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'room_id',
        'customer_id',
        'start_date',
        'end_date',
        'status',
        'deposit_amount',
        'monthly_rent',
        'payment_day',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deposit_amount' => 'float',
        'monthly_rent' => 'float',
        'payment_day' => 'integer',
        'room_id' => 'integer',
        'customer_id' => 'integer'
    ];

    // Validation rules
    public static $rules = [
        'MaHopDong' => 'required|string|max:30|unique:contracts',
        'NguoiThue' => 'required|string|max:30',
        'NgayBatDau' => 'required|date',
        'NgayKetThuc' => 'required|date|after:NgayBatDau',
        'TienCoc' => 'required|numeric|min:0',
        'TienPhong' => 'required|numeric|min:0',
        'SoPhong' => 'required|string|max:50',
        'SoNha' => 'required|string|max:50',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro',
        'MaNhaTro' => 'required|string|exists:buildings,MaNhaTro',
        'MaPhong' => 'required|string|exists:rooms,MaPhong'
    ];

    // Relationships
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price')
            ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
