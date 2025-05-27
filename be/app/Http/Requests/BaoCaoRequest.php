<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaoCaoRequest extends FormRequest
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
            'ngay_tao' => 'required|date',
            'ngay_cap_nhat' => 'nullable|date',
            'noi_dung' => 'required|string',
            'file_path' => 'nullable|string|max:255',
            'toa_nha_id' => 'required|exists:toa_nhas,id',
            'nguoi_tao_id' => 'required|exists:users,id',
            'trang_thai' => 'required|in:draft,published,archived'
        ];
    }

    public function messages()
    {
        return [
            'ten.required' => 'Tên báo cáo không được để trống',
            'ten.max' => 'Tên báo cáo không được vượt quá 255 ký tự',
            'loai.required' => 'Loại báo cáo không được để trống',
            'loai.max' => 'Loại báo cáo không được vượt quá 50 ký tự',
            'ngay_tao.required' => 'Ngày tạo không được để trống',
            'ngay_tao.date' => 'Ngày tạo không hợp lệ',
            'ngay_cap_nhat.date' => 'Ngày cập nhật không hợp lệ',
            'noi_dung.required' => 'Nội dung không được để trống',
            'file_path.max' => 'Đường dẫn file không được vượt quá 255 ký tự',
            'toa_nha_id.required' => 'Tòa nhà không được để trống',
            'toa_nha_id.exists' => 'Tòa nhà không tồn tại',
            'nguoi_tao_id.required' => 'Người tạo không được để trống',
            'nguoi_tao_id.exists' => 'Người tạo không tồn tại',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ'
        ];
    }
}
