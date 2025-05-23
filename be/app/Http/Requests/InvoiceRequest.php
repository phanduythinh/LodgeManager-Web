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
            'invoice_number' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:invoices,invoice_number,' . $this->invoice?->id],
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date', 'before_or_equal:' . now()->addMonths(3)],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'subtotal' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'tax_amount' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'discount_amount' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'status' => ['required', 'string', 'in:pending,paid,overdue,cancelled,partially_paid'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,credit_card,momo,vnpay'],
            'payment_status' => ['required', 'string', 'in:unpaid,partially_paid,paid'],
            'contract_id' => ['required', 'exists:contracts,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'items.*.amount' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_due_reminder' => ['nullable', 'boolean'],
            'late_payment_fee' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required' => 'Số hóa đơn là bắt buộc',
            'invoice_number.unique' => 'Số hóa đơn đã tồn tại',
            'invoice_number.regex' => 'Số hóa đơn chỉ được chứa chữ cái, số và dấu gạch ngang',
            'invoice_number.max' => 'Số hóa đơn không được vượt quá 50 ký tự',
            'issue_date.required' => 'Ngày phát hành là bắt buộc',
            'issue_date.date' => 'Ngày phát hành không hợp lệ',
            'issue_date.before_or_equal' => 'Ngày phát hành không được sau ngày hiện tại',
            'due_date.required' => 'Ngày đến hạn là bắt buộc',
            'due_date.date' => 'Ngày đến hạn không hợp lệ',
            'due_date.after_or_equal' => 'Ngày đến hạn phải sau hoặc bằng ngày phát hành',
            'due_date.before_or_equal' => 'Ngày đến hạn không được quá 3 tháng',
            'total_amount.required' => 'Tổng tiền là bắt buộc',
            'total_amount.min' => 'Tổng tiền không được âm',
            'total_amount.max' => 'Tổng tiền không được vượt quá 1 tỷ',
            'subtotal.required' => 'Tổng tiền trước thuế là bắt buộc',
            'subtotal.min' => 'Tổng tiền trước thuế không được âm',
            'subtotal.max' => 'Tổng tiền trước thuế không được vượt quá 1 tỷ',
            'tax_amount.required' => 'Tiền thuế là bắt buộc',
            'tax_amount.min' => 'Tiền thuế không được âm',
            'tax_amount.max' => 'Tiền thuế không được vượt quá 100 triệu',
            'discount_amount.required' => 'Tiền giảm giá là bắt buộc',
            'discount_amount.min' => 'Tiền giảm giá không được âm',
            'discount_amount.max' => 'Tiền giảm giá không được vượt quá 100 triệu',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'payment_status.required' => 'Trạng thái thanh toán là bắt buộc',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ',
            'contract_id.required' => 'Hợp đồng là bắt buộc',
            'contract_id.exists' => 'Hợp đồng không tồn tại',
            'customer_id.required' => 'Khách hàng là bắt buộc',
            'customer_id.exists' => 'Khách hàng không tồn tại',
            'items.required' => 'Chi tiết hóa đơn là bắt buộc',
            'items.min' => 'Hóa đơn phải có ít nhất một mục',
            'items.*.description.required' => 'Mô tả là bắt buộc',
            'items.*.description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'items.*.quantity.required' => 'Số lượng là bắt buộc',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.quantity.max' => 'Số lượng không được vượt quá 1000',
            'items.*.unit_price.required' => 'Đơn giá là bắt buộc',
            'items.*.unit_price.min' => 'Đơn giá không được âm',
            'items.*.unit_price.max' => 'Đơn giá không được vượt quá 1 tỷ',
            'items.*.amount.required' => 'Thành tiền là bắt buộc',
            'items.*.amount.min' => 'Thành tiền không được âm',
            'items.*.amount.max' => 'Thành tiền không được vượt quá 1 tỷ',
            'items.*.tax_rate.required' => 'Thuế suất là bắt buộc',
            'items.*.tax_rate.min' => 'Thuế suất không được âm',
            'items.*.tax_rate.max' => 'Thuế suất không được vượt quá 100%',
            'items.*.discount_rate.required' => 'Tỷ lệ giảm giá là bắt buộc',
            'items.*.discount_rate.min' => 'Tỷ lệ giảm giá không được âm',
            'items.*.discount_rate.max' => 'Tỷ lệ giảm giá không được vượt quá 100%',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự',
            'late_payment_fee.required' => 'Phí trả chậm là bắt buộc',
            'late_payment_fee.min' => 'Phí trả chậm không được âm',
            'late_payment_fee.max' => 'Phí trả chậm không được vượt quá 1 triệu',
            'bank_account.max' => 'Số tài khoản không được vượt quá 255 ký tự',
            'bank_name.max' => 'Tên ngân hàng không được vượt quá 255 ký tự',
            'transaction_id.max' => 'Mã giao dịch không được vượt quá 255 ký tự',
            'payment_date.date' => 'Ngày thanh toán không hợp lệ',
            'payment_date.before_or_equal' => 'Ngày thanh toán không được sau ngày hiện tại',
        ];
    }
}
