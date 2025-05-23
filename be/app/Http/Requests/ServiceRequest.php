<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', 'string', 'max:50', 'in:kwh,m3,month,time,other'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
            'type' => ['required', 'string', 'in:utility,maintenance,security,cleaning,other'],
            'billing_frequency' => ['required', 'string', 'in:monthly,quarterly,yearly,on_demand'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_mandatory' => ['required', 'boolean'],
            'is_shared' => ['required', 'boolean'],
            'provider_name' => ['nullable', 'string', 'max:255'],
            'provider_contact' => ['nullable', 'string', 'max:255'],
            'provider_phone' => ['nullable', 'string', 'regex:/^[0-9]{10,11}$/'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên dịch vụ là bắt buộc',
            'name.min' => 'Tên dịch vụ phải có ít nhất 3 ký tự',
            'name.max' => 'Tên dịch vụ không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'unit.required' => 'Đơn vị tính là bắt buộc',
            'unit.max' => 'Đơn vị tính không được vượt quá 50 ký tự',
            'unit.in' => 'Đơn vị tính không hợp lệ',
            'price.required' => 'Giá dịch vụ là bắt buộc',
            'price.min' => 'Giá dịch vụ không được âm',
            'price.max' => 'Giá dịch vụ không được vượt quá 1 tỷ',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'type.required' => 'Loại dịch vụ là bắt buộc',
            'type.in' => 'Loại dịch vụ không hợp lệ',
            'billing_frequency.required' => 'Tần suất thanh toán là bắt buộc',
            'billing_frequency.in' => 'Tần suất thanh toán không hợp lệ',
            'tax_rate.required' => 'Thuế suất là bắt buộc',
            'tax_rate.min' => 'Thuế suất không được âm',
            'tax_rate.max' => 'Thuế suất không được vượt quá 100%',
            'is_mandatory.required' => 'Trạng thái bắt buộc là bắt buộc',
            'is_shared.required' => 'Trạng thái chia sẻ là bắt buộc',
            'provider_name.max' => 'Tên nhà cung cấp không được vượt quá 255 ký tự',
            'provider_contact.max' => 'Thông tin liên hệ không được vượt quá 255 ký tự',
            'provider_phone.regex' => 'Số điện thoại nhà cung cấp không hợp lệ',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự',
        ];
    }
}
