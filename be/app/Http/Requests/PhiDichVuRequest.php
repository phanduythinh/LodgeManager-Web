<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhiDichVuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'required|numeric|min:0',
            'don_vi' => 'required|string|max:50',
            'trang_thai' => 'required|in:active,inactive',
            'loai_dich_vu' => 'required|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'ten.required' => 'Tên dịch vụ không được để trống',
            'ten.max' => 'Tên dịch vụ không được vượt quá 255 ký tự',
            'gia.required' => 'Giá dịch vụ không được để trống',
            'gia.numeric' => 'Giá dịch vụ phải là số',
            'gia.min' => 'Giá dịch vụ không được âm',
            'don_vi.required' => 'Đơn vị không được để trống',
            'don_vi.max' => 'Đơn vị không được vượt quá 50 ký tự',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'loai_dich_vu.required' => 'Loại dịch vụ không được để trống',
            'loai_dich_vu.max' => 'Loại dịch vụ không được vượt quá 50 ký tự'
        ];
    }
}
