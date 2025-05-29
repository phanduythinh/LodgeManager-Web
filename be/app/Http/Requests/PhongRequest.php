<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhongRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'MaPhong' => 'required|string|max:50',
            'TenNha' => 'required|string|max:255',
            'TenPhong' => 'required|string|max:255',
            'Tang' => 'required|string|max:50',
            'GiaThue' => 'required|numeric|min:0',
            'DatCoc' => 'required|numeric|min:0',
            'DienTich' => 'required|numeric|min:0',
            'SoKhachToiDa' => 'required|numeric|min:1',
            'TrangThai' => 'required|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'MaPhong.required' => 'Mã phòng là bắt buộc',
            'TenNha.required' => 'Tên nhà là bắt buộc',
            'TenPhong.required' => 'Tên phòng là bắt buộc',
            'Tang.required' => 'Tầng là bắt buộc',
            'GiaThue.required' => 'Giá thuê là bắt buộc',
            'GiaThue.min' => 'Giá thuê phải lớn hơn 0',
            'DatCoc.required' => 'Đặt cọc là bắt buộc',
            'DatCoc.min' => 'Đặt cọc phải lớn hơn 0',
            'DienTich.required' => 'Diện tích là bắt buộc',
            'DienTich.min' => 'Diện tích phải lớn hơn 0',
            'SoKhachToiDa.required' => 'Số khách tối đa là bắt buộc',
            'SoKhachToiDa.min' => 'Số khách tối đa phải lớn hơn 0',
            'TrangThai.required' => 'Trạng thái là bắt buộc'
        ];
    }
}
