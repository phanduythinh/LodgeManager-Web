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
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'address' => ['required', 'string', 'max:255', 'min:5'],
            'description' => ['nullable', 'string', 'max:1000'],
            'total_floors' => ['required', 'integer', 'min:1', 'max:200'],
            'total_rooms' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
            'owner_id' => ['required', 'exists:owners,id'],
            'year_built' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'building_type' => ['required', 'string', 'in:apartment,office,hotel,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên tòa nhà là bắt buộc',
            'name.min' => 'Tên tòa nhà phải có ít nhất 3 ký tự',
            'name.max' => 'Tên tòa nhà không được vượt quá 255 ký tự',
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.min' => 'Địa chỉ phải có ít nhất 5 ký tự',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'total_floors.required' => 'Số tầng là bắt buộc',
            'total_floors.min' => 'Số tầng phải lớn hơn 0',
            'total_floors.max' => 'Số tầng không được vượt quá 200',
            'total_rooms.required' => 'Số phòng là bắt buộc',
            'total_rooms.min' => 'Số phòng phải lớn hơn 0',
            'total_rooms.max' => 'Số phòng không được vượt quá 1000',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'owner_id.required' => 'Chủ sở hữu là bắt buộc',
            'owner_id.exists' => 'Chủ sở hữu không tồn tại',
            'year_built.min' => 'Năm xây dựng không hợp lệ',
            'year_built.max' => 'Năm xây dựng không được lớn hơn năm hiện tại',
            'building_type.required' => 'Loại tòa nhà là bắt buộc',
            'building_type.in' => 'Loại tòa nhà không hợp lệ',
        ];
    }
}
