<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CustomerRequest",
 *     title="Customer Request",
 *     description="Request body for creating/updating a customer",
 *     required={"name", "email", "phone", "status"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="0123456789"),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive"},
 *         example="active"
 *     ),
 *     @OA\Property(property="address", type="string", nullable=true, example="123 Main St"),
 *     @OA\Property(property="id_card", type="string", nullable=true, example="123456789"),
 *     @OA\Property(property="id_card_issue_date", type="string", format="date", nullable=true, example="2020-01-01"),
 *     @OA\Property(property="id_card_issue_place", type="string", nullable=true, example="Ha Noi"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Customer note")
 * )
 */
class CustomerRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'address' => ['nullable', 'string', 'max:255'],
            'id_card' => ['nullable', 'string', 'max:20'],
            'id_card_issue_date' => ['nullable', 'date'],
            'id_card_issue_place' => ['nullable', 'string', 'max:255'],
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
            'name.required' => 'Tên khách hàng là bắt buộc',
            'name.max' => 'Tên khách hàng không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được vượt quá 255 ký tự',
            'phone.required' => 'Số điện thoại là bắt buộc',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'id_card.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự',
            'id_card_issue_date.date' => 'Ngày cấp CMND/CCCD không hợp lệ',
            'id_card_issue_place.max' => 'Nơi cấp CMND/CCCD không được vượt quá 255 ký tự'
        ];
    }
}
