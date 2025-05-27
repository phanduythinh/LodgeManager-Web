<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HopDongRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ma_hop_dong' => 'required|string|max:50|unique:hop_dongs,ma_hop_dong,' . $this->id,
            'phong_id' => 'required|exists:phongs,id',
            'khach_hang_id' => 'required|exists:khach_hangs,id',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'gia_phong' => 'required|numeric|min:0',
            'dat_coc' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|in:dang_thue,da_ket_thuc,da_huy',
            'dich_vu_ids' => 'nullable|array',
            'dich_vu_ids.*' => 'exists:dich_vus,id'
        ];
    }

    public function messages()
    {
        return [
            'ma_hop_dong.required' => 'Mã hợp đồng không được để trống',
            'ma_hop_dong.unique' => 'Mã hợp đồng đã tồn tại',
            'phong_id.required' => 'Phòng không được để trống',
            'phong_id.exists' => 'Phòng không tồn tại',
            'khach_hang_id.required' => 'Khách hàng không được để trống',
            'khach_hang_id.exists' => 'Khách hàng không tồn tại',
            'ngay_bat_dau.required' => 'Ngày bắt đầu không được để trống',
            'ngay_bat_dau.date' => 'Ngày bắt đầu không hợp lệ',
            'ngay_ket_thuc.required' => 'Ngày kết thúc không được để trống',
            'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'gia_phong.required' => 'Giá phòng không được để trống',
            'gia_phong.numeric' => 'Giá phòng phải là số',
            'gia_phong.min' => 'Giá phòng không được âm',
            'dat_coc.required' => 'Đặt cọc không được để trống',
            'dat_coc.numeric' => 'Đặt cọc phải là số',
            'dat_coc.min' => 'Đặt cọc không được âm',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'dich_vu_ids.array' => 'Danh sách dịch vụ không hợp lệ',
            'dich_vu_ids.*.exists' => 'Dịch vụ không tồn tại'
        ];
    }
}
