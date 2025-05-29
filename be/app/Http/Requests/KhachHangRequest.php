<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Lấy ID từ route nếu có
        $id = $this->route('khach_hang');
        
        return [
            'ho_ten' => 'required|string|max:255',
            'email' => 'nullable|email|unique:khach_hangs,email,' . $id,
            'so_dien_thoai' => 'required|string|max:20',
            'cccd' => 'nullable|string|max:20|unique:khach_hangs,cccd,' . $id,
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|string',
            'dia_chi_nha' => 'nullable|string|max:255',
            'xa_phuong' => 'nullable|string|max:100',
            'quan_huyen' => 'nullable|string|max:100',
            'tinh_thanh' => 'nullable|string|max:100',
            'ma_khach_hang' => 'nullable|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'ho_ten.required' => 'Họ tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc',
            'cmnd_cccd.required' => 'CMND/CCCD là bắt buộc',
            'cmnd_cccd.unique' => 'CMND/CCCD đã tồn tại',
            'ngay_sinh.required' => 'Ngày sinh là bắt buộc',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ',
            'gioi_tinh.required' => 'Giới tính là bắt buộc',
            'gioi_tinh.in' => 'Giới tính không hợp lệ',
            'dia_chi.required' => 'Địa chỉ là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
