<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiayToRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten' => 'required|string|max:255',
            'loai' => 'required|string|max:50',
            'ngay_cap' => 'required|date',
            'noi_cap' => 'required|string|max:255',
            'ngay_het_han' => 'nullable|date|after:ngay_cap',
            'file_path' => 'nullable|string|max:255',
            'toa_nha_id' => 'required|exists:toa_nhas,id',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'ten.required' => 'Tên giấy tờ không được để trống',
            'ten.max' => 'Tên giấy tờ không được vượt quá 255 ký tự',
            'loai.required' => 'Loại giấy tờ không được để trống',
            'loai.max' => 'Loại giấy tờ không được vượt quá 50 ký tự',
            'ngay_cap.required' => 'Ngày cấp không được để trống',
            'ngay_cap.date' => 'Ngày cấp không hợp lệ',
            'noi_cap.required' => 'Nơi cấp không được để trống',
            'noi_cap.max' => 'Nơi cấp không được vượt quá 255 ký tự',
            'ngay_het_han.date' => 'Ngày hết hạn không hợp lệ',
            'ngay_het_han.after' => 'Ngày hết hạn phải sau ngày cấp',
            'file_path.max' => 'Đường dẫn file không được vượt quá 255 ký tự',
            'toa_nha_id.required' => 'Tòa nhà không được để trống',
            'toa_nha_id.exists' => 'Tòa nhà không tồn tại',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
