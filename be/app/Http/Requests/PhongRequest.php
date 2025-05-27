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
            'ten' => 'required|string|max:255',
            'toa_nha_id' => 'required|exists:toa_nha,id',
            'loai_phong' => 'required|string|max:50',
            'dien_tich' => 'required|numeric|min:0',
            'gia_thue' => 'required|numeric|min:0',
            'trang_thai' => 'required|in:trong,da_thue,bao_tri',
            'mo_ta' => 'nullable|string',
            'tien_ich' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'ten.required' => 'Tên phòng là bắt buộc',
            'toa_nha_id.required' => 'Tòa nhà là bắt buộc',
            'toa_nha_id.exists' => 'Tòa nhà không tồn tại',
            'loai_phong.required' => 'Loại phòng là bắt buộc',
            'dien_tich.required' => 'Diện tích là bắt buộc',
            'dien_tich.min' => 'Diện tích phải lớn hơn 0',
            'gia_thue.required' => 'Giá thuê là bắt buộc',
            'gia_thue.min' => 'Giá thuê phải lớn hơn 0',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
