<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RoomRequest",
 *     title="Room Request",
 *     description="Request body for creating/updating a room",
 *     required={"room_number", "building_id", "status", "price"},
 *     @OA\Property(property="room_number", type="string", example="101"),
 *     @OA\Property(property="building_id", type="integer", example=1),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"available", "occupied", "maintenance"},
 *         example="available"
 *     ),
 *     @OA\Property(property="price", type="number", format="float", example=2000000),
 *     @OA\Property(property="area", type="number", format="float", example=25.5),
 *     @OA\Property(property="floor", type="integer", example=1),
 *     @OA\Property(property="description", type="string", nullable=true, example="Room with balcony")
 * )
 */
class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_number' => ['required', 'string', 'max:10'],
            'building_id' => ['required', 'exists:buildings,id'],
            'status' => ['required', 'string', 'in:available,occupied,maintenance'],
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'floor' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'room_number.required' => 'Số phòng là bắt buộc',
            'room_number.max' => 'Số phòng không được vượt quá 10 ký tự',
            'building_id.required' => 'Tòa nhà là bắt buộc',
            'building_id.exists' => 'Tòa nhà không tồn tại',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'price.required' => 'Giá phòng là bắt buộc',
            'price.numeric' => 'Giá phòng phải là số',
            'price.min' => 'Giá phòng phải lớn hơn 0',
            'area.numeric' => 'Diện tích phải là số',
            'area.min' => 'Diện tích phải lớn hơn 0',
            'floor.required' => 'Tầng là bắt buộc',
            'floor.integer' => 'Tầng phải là số nguyên',
            'floor.min' => 'Tầng phải lớn hơn 0'
        ];
    }
}
