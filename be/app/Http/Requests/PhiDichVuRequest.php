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
            'MaDichVu' => 'required|string|max:50',
            'TenDichVu' => 'required|string|max:255',
            'LoaiDichVu' => 'required|string|max:50',
            'DonGia' => 'required|numeric|min:0',
            'DonViTinh' => 'required|string|max:50',
            'TenNha' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'MaDichVu.required' => 'Mã dịch vụ không được để trống',
            'MaDichVu.max' => 'Mã dịch vụ không được vượt quá 50 ký tự',
            'TenDichVu.required' => 'Tên dịch vụ không được để trống',
            'TenDichVu.max' => 'Tên dịch vụ không được vượt quá 255 ký tự',
            'LoaiDichVu.required' => 'Loại dịch vụ không được để trống',
            'LoaiDichVu.max' => 'Loại dịch vụ không được vượt quá 50 ký tự',
            'DonGia.required' => 'Đơn giá không được để trống',
            'DonGia.numeric' => 'Đơn giá phải là số',
            'DonGia.min' => 'Đơn giá không được âm',
            'DonViTinh.required' => 'Đơn vị tính không được để trống',
            'DonViTinh.max' => 'Đơn vị tính không được vượt quá 50 ký tự',
            'TenNha.required' => 'Tên nhà không được để trống',
            'TenNha.max' => 'Tên nhà không được vượt quá 255 ký tự'
        ];
    }
}
