<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="InvoiceRequest",
 *     title="Invoice Request",
 *     description="Request body for creating/updating an invoice",
 *     required={"invoice_number", "customer_id", "contract_id", "issue_date", "due_date", "total_amount", "status"},
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
 *     @OA\Property(property="services", type="array", @OA\Items(type="integer"), example=[1, 2, 3]),
 *     @OA\Property(property="note", type="string", nullable=true, example="Invoice note")
 * )
 */
class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number,' . $this->invoice?->id],
            'customer_id' => ['required', 'exists:customers,id'],
            'contract_id' => ['required', 'exists:contracts,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid,overdue,cancelled'],
            'services' => ['required', 'array'],
            'services.*' => ['exists:services,id'],
            'note' => ['nullable', 'string']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_number.required' => 'Số hóa đơn là bắt buộc',
            'invoice_number.unique' => 'Số hóa đơn đã tồn tại',
            'customer_id.required' => 'Khách hàng là bắt buộc',
            'customer_id.exists' => 'Khách hàng không tồn tại',
            'contract_id.required' => 'Hợp đồng là bắt buộc',
            'contract_id.exists' => 'Hợp đồng không tồn tại',
            'issue_date.required' => 'Ngày phát hành là bắt buộc',
            'issue_date.date' => 'Ngày phát hành không hợp lệ',
            'due_date.required' => 'Ngày đến hạn là bắt buộc',
            'due_date.date' => 'Ngày đến hạn không hợp lệ',
            'due_date.after_or_equal' => 'Ngày đến hạn phải sau hoặc bằng ngày phát hành',
            'total_amount.required' => 'Tổng tiền là bắt buộc',
            'total_amount.numeric' => 'Tổng tiền phải là số',
            'total_amount.min' => 'Tổng tiền không được âm',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'services.required' => 'Dịch vụ là bắt buộc',
            'services.array' => 'Dịch vụ phải là một mảng',
            'services.*.exists' => 'Dịch vụ không tồn tại'
        ];
    }
}
