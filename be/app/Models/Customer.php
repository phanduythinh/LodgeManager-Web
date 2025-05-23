<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Customer",
 *     title="Customer",
 *     description="Customer model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="0123456789"),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive"},
 *         example="active"
 *     ),
 *     @OA\Property(property="address", type="string", nullable=true, example="123 Main St"),
 *     @OA\Property(property="id_card", type="string", nullable=true, example="123456789"),
 *     @OA\Property(property="id_card_issue_date", type="string", format="date", nullable=true, example="2020-01-01"),
 *     @OA\Property(property="id_card_issue_place", type="string", nullable=true, example="Ha Noi"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Customer note"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'MaKhachHang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'address',
        'id_card',
        'id_card_issue_date',
        'id_card_issue_place',
        'note'
    ];

    protected $casts = [
        'id_card_issue_date' => 'date'
    ];

    // Validation rules
    public static $rules = [
        'MaKhachHang' => 'required|string|max:10|unique:customers',
        'TenKhachHang' => 'required|string|max:30',
        'SDT' => 'required|string|max:10',
        'Email' => 'required|email|max:50|unique:customers',
        'DiaChiThuongTru' => 'required|string|max:50',
        'NgaySinh' => 'required|date',
        'CCCD' => 'required|string|max:50',
        'MaChuTro' => 'required|string|exists:owners,MaChuTro'
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'MaChuTro', 'MaChuTro');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class, 'MaKhachHang', 'MaKhachHang');
    }
}
