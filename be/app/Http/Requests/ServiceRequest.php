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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'type' => ['required', 'string', 'in:utility,maintenance,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên dịch vụ là bắt buộc',
            'unit.required' => 'Đơn vị tính là bắt buộc',
            'price.required' => 'Giá dịch vụ là bắt buộc',
            'price.min' => 'Giá dịch vụ không được âm',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'type.required' => 'Loại dịch vụ là bắt buộc',
            'type.in' => 'Loại dịch vụ không hợp lệ',
        ];
    }
} 