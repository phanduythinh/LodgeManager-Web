<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Invoice",
 *     title="Invoice",
 *     description="Invoice model",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="invoice_number", type="string", example="INV-2024-001"),
 *     @OA\Property(property="customer_id", type="integer", example=1),
 *     @OA\Property(property="contract_id", type="integer", example=1),
 *     @OA\Property(property="issue_date", type="string", format="date", example="2024-03-20"),
 *     @OA\Property(property="due_date", type="string", format="date", example="2024-04-20"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=2000000),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "paid", "overdue", "cancelled"},
 *         example="pending"
 *     ),
 *     @OA\Property(property="payment_date", type="string", format="date", nullable=true, example="2024-03-25"),
 *     @OA\Property(
 *         property="payment_method",
 *         type="string",
 *         enum={"cash", "bank_transfer", "credit_card"},
 *         nullable=true,
 *         example="bank_transfer"
 *     ),
 *     @OA\Property(property="transaction_id", type="string", nullable=true, example="TRX123456"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Invoice note"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';
    protected $primaryKey = 'MaHoaDon';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'contract_id',
        'issue_date',
        'due_date',
        'total_amount',
        'status',
        'payment_date',
        'payment_method',
        'transaction_id',
        'note'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'total_amount' => 'float'
    ];

    // Validation rules
    public static $rules = [
        'invoice_number' => 'required|string|max:30|unique:invoices',
        'customer_id' => 'required|integer|exists:customers,id',
        'contract_id' => 'required|integer|exists:contracts,id',
        'issue_date' => 'required|date',
        'due_date' => 'required|date',
        'total_amount' => 'required|numeric|min:0',
        'status' => 'required|string|in:pending,paid,overdue,cancelled',
        'payment_date' => 'nullable|date',
        'payment_method' => 'nullable|string|in:cash,bank_transfer,credit_card',
        'transaction_id' => 'nullable|string|max:50',
        'note' => 'nullable|string|max:255'
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'invoice_services')
            ->withPivot(['quantity', 'price', 'amount'])
            ->withTimestamps();
    }
}
