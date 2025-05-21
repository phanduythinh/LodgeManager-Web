<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_number' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'integer', 'min:1'],
            'area' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:available,occupied,maintenance'],
            'description' => ['nullable', 'string'],
            'building_id' => ['required', 'exists:buildings,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_number.required' => 'Số phòng là bắt buộc',
            'floor.required' => 'Tầng là bắt buộc',
            'floor.min' => 'Tầng phải lớn hơn 0',
            'area.required' => 'Diện tích là bắt buộc',
            'area.min' => 'Diện tích phải lớn hơn 0',
            'price.required' => 'Giá phòng là bắt buộc',
            'price.min' => 'Giá phòng không được âm',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'building_id.required' => 'Tòa nhà là bắt buộc',
            'building_id.exists' => 'Tòa nhà không tồn tại',
        ];
    }
}
