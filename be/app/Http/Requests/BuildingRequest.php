<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="BuildingRequest",
 *     title="Building Request",
 *     description="Request body for creating/updating a building",
 *     required={"name", "address", "status"},
 *     @OA\Property(property="name", type="string", minLength=3, maxLength=255, example="Sunshine Apartment"),
 *     @OA\Property(property="address", type="string", maxLength=255, example="123 Main St"),
 *     @OA\Property(property="description", type="string", nullable=true, example="A modern apartment complex"),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive", "maintenance"},
 *         example="active"
 *     ),
 *     @OA\Property(property="total_floors", type="integer", minimum=1, example=10),
 *     @OA\Property(property="total_rooms", type="integer", minimum=1, example=50),
 *     @OA\Property(property="year_built", type="integer", minimum=1900, maximum=2100, example=2020),
 *     @OA\Property(property="owner_id", type="integer", example=1)
 * )
 */
class BuildingRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance'],
            'total_floors' => ['required', 'integer', 'min:1'],
            'total_rooms' => ['required', 'integer', 'min:1'],
            'year_built' => ['required', 'integer', 'min:1900', 'max:2100'],
            'owner_id' => ['required', 'exists:users,id']
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
            'name.required' => 'Tên tòa nhà là bắt buộc',
            'name.min' => 'Tên tòa nhà phải có ít nhất 3 ký tự',
            'name.max' => 'Tên tòa nhà không được vượt quá 255 ký tự',
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'total_floors.required' => 'Số tầng là bắt buộc',
            'total_floors.min' => 'Số tầng phải lớn hơn 0',
            'total_rooms.required' => 'Tổng số phòng là bắt buộc',
            'total_rooms.min' => 'Tổng số phòng phải lớn hơn 0',
            'year_built.required' => 'Năm xây dựng là bắt buộc',
            'year_built.min' => 'Năm xây dựng không hợp lệ',
            'year_built.max' => 'Năm xây dựng không hợp lệ',
            'owner_id.required' => 'Chủ sở hữu là bắt buộc',
            'owner_id.exists' => 'Chủ sở hữu không tồn tại'
        ];
    }
}
