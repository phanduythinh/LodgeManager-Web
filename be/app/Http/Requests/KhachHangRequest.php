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
        return [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:khach_hang,email,' . $this->id,
            'so_dien_thoai' => 'required|string|max:20',
            'cmnd_cccd' => 'required|string|max:20|unique:khach_hang,cmnd_cccd,' . $this->id,
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'dia_chi' => 'required|string|max:255',
            'nghe_nghiep' => 'nullable|string|max:100',
            'trang_thai' => 'required|in:hoat_dong,khong_hoat_dong',
            'ghi_chu' => 'nullable|string'
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
