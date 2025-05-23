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
            'room_number' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-]+$/'],
            'floor' => ['required', 'integer', 'min:1', 'max:200'],
            'area' => ['required', 'numeric', 'min:1', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'status' => ['required', 'string', 'in:available,occupied,maintenance,reserved'],
            'description' => ['nullable', 'string', 'max:1000'],
            'building_id' => ['required', 'exists:buildings,id'],
            'room_type' => ['required', 'string', 'in:standard,deluxe,suite,studio'],
            'max_occupants' => ['required', 'integer', 'min:1', 'max:10'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'in:air_conditioning,wifi,tv,refrigerator,balcony'],
            'is_furnished' => ['required', 'boolean'],
            'deposit_amount' => ['required', 'numeric', 'min:0', 'max:100000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_number.required' => 'Số phòng là bắt buộc',
            'room_number.regex' => 'Số phòng chỉ được chứa chữ cái, số và dấu gạch ngang',
            'room_number.max' => 'Số phòng không được vượt quá 50 ký tự',
            'floor.required' => 'Tầng là bắt buộc',
            'floor.min' => 'Tầng phải lớn hơn 0',
            'floor.max' => 'Tầng không được vượt quá 200',
            'area.required' => 'Diện tích là bắt buộc',
            'area.min' => 'Diện tích phải lớn hơn 0',
            'area.max' => 'Diện tích không được vượt quá 1000m²',
            'price.required' => 'Giá phòng là bắt buộc',
            'price.min' => 'Giá phòng không được âm',
            'price.max' => 'Giá phòng không được vượt quá 1 tỷ',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'building_id.required' => 'Tòa nhà là bắt buộc',
            'building_id.exists' => 'Tòa nhà không tồn tại',
            'room_type.required' => 'Loại phòng là bắt buộc',
            'room_type.in' => 'Loại phòng không hợp lệ',
            'max_occupants.required' => 'Số người tối đa là bắt buộc',
            'max_occupants.min' => 'Số người tối đa phải lớn hơn 0',
            'max_occupants.max' => 'Số người tối đa không được vượt quá 10',
            'amenities.array' => 'Tiện nghi phải là một mảng',
            'amenities.*.in' => 'Tiện nghi không hợp lệ',
            'is_furnished.required' => 'Trạng thái nội thất là bắt buộc',
            'deposit_amount.required' => 'Số tiền đặt cọc là bắt buộc',
            'deposit_amount.min' => 'Số tiền đặt cọc không được âm',
            'deposit_amount.max' => 'Số tiền đặt cọc không được vượt quá 100 triệu',
        ];
    }
}
