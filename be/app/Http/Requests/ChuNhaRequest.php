<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChuNhaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'email' => 'required|email',
            'dia_chi' => 'required|string|max:255',
            'cmnd' => 'required|string|max:20',
            'ngay_cap_cmnd' => 'required|date',
            'noi_cap_cmnd' => 'required|string|max:255',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'trang_thai' => 'required|in:active,inactive'
        ];

        // Nếu là cập nhật, thêm rule unique cho email
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['email'] = 'required|email|unique:chu_nhas,email,' . $this->route('id');
        } else {
            $rules['email'] = 'required|email|unique:chu_nhas';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'ho_ten.required' => 'Họ tên không được để trống',
            'ho_ten.max' => 'Họ tên không được vượt quá 255 ký tự',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống',
            'so_dien_thoai.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'dia_chi.required' => 'Địa chỉ không được để trống',
            'dia_chi.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'cmnd.required' => 'CMND/CCCD không được để trống',
            'cmnd.max' => 'CMND/CCCD không được vượt quá 20 ký tự',
            'ngay_cap_cmnd.required' => 'Ngày cấp CMND/CCCD không được để trống',
            'ngay_cap_cmnd.date' => 'Ngày cấp CMND/CCCD không hợp lệ',
            'noi_cap_cmnd.required' => 'Nơi cấp CMND/CCCD không được để trống',
            'noi_cap_cmnd.max' => 'Nơi cấp CMND/CCCD không được vượt quá 255 ký tự',
            'ngay_sinh.required' => 'Ngày sinh không được để trống',
            'ngay_sinh.date' => 'Ngày sinh không hợp lệ',
            'gioi_tinh.required' => 'Giới tính không được để trống',
            'gioi_tinh.in' => 'Giới tính không hợp lệ',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
