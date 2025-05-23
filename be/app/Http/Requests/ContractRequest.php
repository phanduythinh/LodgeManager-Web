<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_number' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:contracts,contract_number,' . $this->contract?->id],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date', 'before_or_equal:' . now()->addYears(10)],
            'rental_price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'deposit' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'status' => ['required', 'string', 'in:active,expired,terminated,pending'],
            'room_id' => ['required', 'exists:rooms,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.price' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'payment_frequency' => ['required', 'string', 'in:monthly,quarterly,yearly'],
            'payment_due_day' => ['required', 'integer', 'min:1', 'max:31'],
            'late_payment_fee' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'early_termination_fee' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'additional_terms' => ['nullable', 'string', 'max:2000'],
            'witness_name' => ['nullable', 'string', 'max:255'],
            'witness_phone' => ['nullable', 'string', 'regex:/^[0-9]{10,11}$/'],
            'witness_address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_number.required' => 'Số hợp đồng là bắt buộc',
            'contract_number.unique' => 'Số hợp đồng đã tồn tại',
            'contract_number.regex' => 'Số hợp đồng chỉ được chứa chữ cái, số và dấu gạch ngang',
            'contract_number.max' => 'Số hợp đồng không được vượt quá 50 ký tự',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải từ hôm nay trở đi',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'end_date.before_or_equal' => 'Ngày kết thúc không được quá 10 năm',
            'rental_price.required' => 'Giá thuê là bắt buộc',
            'rental_price.min' => 'Giá thuê không được âm',
            'rental_price.max' => 'Giá thuê không được vượt quá 1 tỷ',
            'deposit.required' => 'Tiền cọc là bắt buộc',
            'deposit.min' => 'Tiền cọc không được âm',
            'deposit.max' => 'Tiền cọc không được vượt quá 100 triệu',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'room_id.required' => 'Phòng là bắt buộc',
            'room_id.exists' => 'Phòng không tồn tại',
            'customer_id.required' => 'Khách hàng là bắt buộc',
            'customer_id.exists' => 'Khách hàng không tồn tại',
            'services.*.service_id.required' => 'Dịch vụ là bắt buộc',
            'services.*.service_id.exists' => 'Dịch vụ không tồn tại',
            'services.*.price.required' => 'Giá dịch vụ là bắt buộc',
            'services.*.price.min' => 'Giá dịch vụ không được âm',
            'services.*.price.max' => 'Giá dịch vụ không được vượt quá 100 triệu',
            'payment_frequency.required' => 'Tần suất thanh toán là bắt buộc',
            'payment_frequency.in' => 'Tần suất thanh toán không hợp lệ',
            'payment_due_day.required' => 'Ngày đến hạn thanh toán là bắt buộc',
            'payment_due_day.min' => 'Ngày đến hạn phải từ 1 đến 31',
            'payment_due_day.max' => 'Ngày đến hạn phải từ 1 đến 31',
            'late_payment_fee.required' => 'Phí trả chậm là bắt buộc',
            'late_payment_fee.min' => 'Phí trả chậm không được âm',
            'late_payment_fee.max' => 'Phí trả chậm không được vượt quá 1 triệu',
            'early_termination_fee.required' => 'Phí chấm dứt sớm là bắt buộc',
            'early_termination_fee.min' => 'Phí chấm dứt sớm không được âm',
            'early_termination_fee.max' => 'Phí chấm dứt sớm không được vượt quá 10 triệu',
            'additional_terms.max' => 'Điều khoản bổ sung không được vượt quá 2000 ký tự',
            'witness_name.max' => 'Tên người làm chứng không được vượt quá 255 ký tự',
            'witness_phone.regex' => 'Số điện thoại người làm chứng không hợp lệ',
            'witness_address.max' => 'Địa chỉ người làm chứng không được vượt quá 255 ký tự',
        ];
    }
}
