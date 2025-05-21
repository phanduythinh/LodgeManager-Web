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
            'contract_number' => ['required', 'string', 'max:50', 'unique:contracts,contract_number,' . $this->contract?->id],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,expired,terminated'],
            'room_id' => ['required', 'exists:rooms,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_number.required' => 'Số hợp đồng là bắt buộc',
            'contract_number.unique' => 'Số hợp đồng đã tồn tại',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.required' => 'Ngày kết thúc là bắt buộc',
            'end_date.date' => 'Ngày kết thúc không hợp lệ',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'rental_price.required' => 'Giá thuê là bắt buộc',
            'rental_price.min' => 'Giá thuê không được âm',
            'deposit.required' => 'Tiền cọc là bắt buộc',
            'deposit.min' => 'Tiền cọc không được âm',
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
        ];
    }
}
