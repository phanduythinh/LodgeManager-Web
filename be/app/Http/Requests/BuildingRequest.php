<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_floors' => ['required', 'integer', 'min:1'],
            'total_rooms' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'owner_id' => ['required', 'exists:owners,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên tòa nhà là bắt buộc',
            'address.required' => 'Địa chỉ là bắt buộc',
            'total_floors.required' => 'Số tầng là bắt buộc',
            'total_floors.min' => 'Số tầng phải lớn hơn 0',
            'total_rooms.required' => 'Số phòng là bắt buộc',
            'total_rooms.min' => 'Số phòng phải lớn hơn 0',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'owner_id.required' => 'Chủ sở hữu là bắt buộc',
            'owner_id.exists' => 'Chủ sở hữu không tồn tại',
        ];
    }
}
