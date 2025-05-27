<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToaNhaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten' => 'required|string|max:255',
            'dia_chi' => 'required|string|max:255',
            'so_tang' => 'required|integer|min:1',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_hoat_dong',
            'chu_toa_nha_id' => 'required|exists:chu_toa_nha,id'
        ];
    }

    public function messages()
    {
        return [
            'ten.required' => 'Tên tòa nhà là bắt buộc',
            'dia_chi.required' => 'Địa chỉ là bắt buộc',
            'so_tang.required' => 'Số tầng là bắt buộc',
            'so_tang.min' => 'Số tầng phải lớn hơn 0',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'chu_toa_nha_id.required' => 'Chủ tòa nhà là bắt buộc',
            'chu_toa_nha_id.exists' => 'Chủ tòa nhà không tồn tại'
        ];
    }
}
