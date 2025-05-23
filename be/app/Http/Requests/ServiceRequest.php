<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ServiceRequest",
 *     title="Service Request",
 *     description="Request body for creating/updating a service",
 *     required={"name", "price", "status"},
 *     @OA\Property(property="name", type="string", example="Internet"),
 *     @OA\Property(property="description", type="string", nullable=true, example="High-speed internet service"),
 *     @OA\Property(property="price", type="number", format="float", example=200000),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive"},
 *         example="active"
 *     ),
 *     @OA\Property(property="note", type="string", nullable=true, example="Service note")
 * )
 */
class ServiceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'note' => ['nullable', 'string']
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
            'name.required' => 'Tên dịch vụ là bắt buộc',
            'name.max' => 'Tên dịch vụ không được vượt quá 255 ký tự',
            'price.required' => 'Giá dịch vụ là bắt buộc',
            'price.numeric' => 'Giá dịch vụ phải là số',
            'price.min' => 'Giá dịch vụ không được âm',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
