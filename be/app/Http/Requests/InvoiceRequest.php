<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number,' . $this->invoice?->id],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid,overdue,cancelled'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,credit_card'],
            'contract_id' => ['required', 'exists:contracts,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required' => 'Số hóa đơn là bắt buộc',
            'invoice_number.unique' => 'Số hóa đơn đã tồn tại',
            'issue_date.required' => 'Ngày phát hành là bắt buộc',
            'issue_date.date' => 'Ngày phát hành không hợp lệ',
            'due_date.required' => 'Ngày đến hạn là bắt buộc',
            'due_date.date' => 'Ngày đến hạn không hợp lệ',
            'due_date.after_or_equal' => 'Ngày đến hạn phải sau hoặc bằng ngày phát hành',
            'total_amount.required' => 'Tổng tiền là bắt buộc',
            'total_amount.min' => 'Tổng tiền không được âm',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'contract_id.required' => 'Hợp đồng là bắt buộc',
            'contract_id.exists' => 'Hợp đồng không tồn tại',
            'customer_id.required' => 'Khách hàng là bắt buộc',
            'customer_id.exists' => 'Khách hàng không tồn tại',
            'items.required' => 'Chi tiết hóa đơn là bắt buộc',
            'items.min' => 'Hóa đơn phải có ít nhất một mục',
            'items.*.description.required' => 'Mô tả là bắt buộc',
            'items.*.quantity.required' => 'Số lượng là bắt buộc',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.unit_price.required' => 'Đơn giá là bắt buộc',
            'items.*.unit_price.min' => 'Đơn giá không được âm',
            'items.*.amount.required' => 'Thành tiền là bắt buộc',
            'items.*.amount.min' => 'Thành tiền không được âm',
        ];
    }
}
