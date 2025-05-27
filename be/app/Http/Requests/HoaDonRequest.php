<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HoaDonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ma_hoa_don' => 'required|string|max:50|unique:hoa_dons,ma_hoa_don,' . $this->id,
            'hop_dong_id' => 'required|exists:hop_dongs,id',
            'ngay_tao' => 'required|date',
            'ngay_thanh_toan' => 'nullable|date',
            'tong_tien' => 'required|numeric|min:0',
            'tien_phong' => 'required|numeric|min:0',
            'tien_dich_vu' => 'required|numeric|min:0',
            'tien_no' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|in:chua_thanh_toan,da_thanh_toan,da_huy',
            'chi_tiet_dich_vu' => 'required|array',
            'chi_tiet_dich_vu.*.dich_vu_id' => 'required|exists:dich_vus,id',
            'chi_tiet_dich_vu.*.so_luong' => 'required|numeric|min:0',
            'chi_tiet_dich_vu.*.don_gia' => 'required|numeric|min:0',
            'chi_tiet_dich_vu.*.thanh_tien' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'ma_hoa_don.required' => 'Mã hóa đơn không được để trống',
            'ma_hoa_don.unique' => 'Mã hóa đơn đã tồn tại',
            'hop_dong_id.required' => 'Hợp đồng không được để trống',
            'hop_dong_id.exists' => 'Hợp đồng không tồn tại',
            'ngay_tao.required' => 'Ngày tạo không được để trống',
            'ngay_tao.date' => 'Ngày tạo không hợp lệ',
            'ngay_thanh_toan.date' => 'Ngày thanh toán không hợp lệ',
            'tong_tien.required' => 'Tổng tiền không được để trống',
            'tong_tien.numeric' => 'Tổng tiền phải là số',
            'tong_tien.min' => 'Tổng tiền không được âm',
            'tien_phong.required' => 'Tiền phòng không được để trống',
            'tien_phong.numeric' => 'Tiền phòng phải là số',
            'tien_phong.min' => 'Tiền phòng không được âm',
            'tien_dich_vu.required' => 'Tiền dịch vụ không được để trống',
            'tien_dich_vu.numeric' => 'Tiền dịch vụ phải là số',
            'tien_dich_vu.min' => 'Tiền dịch vụ không được âm',
            'tien_no.required' => 'Tiền nợ không được để trống',
            'tien_no.numeric' => 'Tiền nợ phải là số',
            'tien_no.min' => 'Tiền nợ không được âm',
            'trang_thai.required' => 'Trạng thái không được để trống',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'chi_tiet_dich_vu.required' => 'Chi tiết dịch vụ không được để trống',
            'chi_tiet_dich_vu.array' => 'Chi tiết dịch vụ không hợp lệ',
            'chi_tiet_dich_vu.*.dich_vu_id.required' => 'Dịch vụ không được để trống',
            'chi_tiet_dich_vu.*.dich_vu_id.exists' => 'Dịch vụ không tồn tại',
            'chi_tiet_dich_vu.*.so_luong.required' => 'Số lượng không được để trống',
            'chi_tiet_dich_vu.*.so_luong.numeric' => 'Số lượng phải là số',
            'chi_tiet_dich_vu.*.so_luong.min' => 'Số lượng không được âm',
            'chi_tiet_dich_vu.*.don_gia.required' => 'Đơn giá không được để trống',
            'chi_tiet_dich_vu.*.don_gia.numeric' => 'Đơn giá phải là số',
            'chi_tiet_dich_vu.*.don_gia.min' => 'Đơn giá không được âm',
            'chi_tiet_dich_vu.*.thanh_tien.required' => 'Thành tiền không được để trống',
            'chi_tiet_dich_vu.*.thanh_tien.numeric' => 'Thành tiền phải là số',
            'chi_tiet_dich_vu.*.thanh_tien.min' => 'Thành tiền không được âm'
        ];
    }
}
