<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HopDongController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $hopDongs = HopDong::with(['phong.toaNha', 'khachHangs', 'phiDichVus'])->get();
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedHopDongs = $hopDongs->map(function ($hopDong) {
                return [
                    'id' => $hopDong->id,
                    'MaHopDong' => $hopDong->ma_hop_dong,
                    'MaPhongId' => $hopDong->phong_id,
                    'TenPhong' => $hopDong->phong ? $hopDong->phong->ten_phong : '',
                    'MaNhaId' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ma_nha : null,
                    'TenNha' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ten_nha : '',
                    'NgayBatDau' => $hopDong->ngay_bat_dau,
                    'NgayKetThuc' => $hopDong->ngay_ket_thuc,
                    'TienThue' => $hopDong->tien_thue,
                    'TienCoc' => $hopDong->tien_coc,
                    'ChuKyThanhToan' => $hopDong->chu_ky_thanh_toan,
                    'NgayTinhTien' => $hopDong->ngay_tinh_tien,
                    'TrangThai' => $hopDong->trang_thai,
                    'KhachHangs' => $hopDong->khachHangs ? $hopDong->khachHangs->map(function ($khachHang) {
                        return [
                            'id' => $khachHang->id,
                            'MaKhachHang' => $khachHang->ma_khach_hang,
                            'HoTen' => $khachHang->ho_ten,
                            'NgaySinh' => $khachHang->ngay_sinh,
                            'GioiTinh' => $khachHang->gioi_tinh ? $khachHang->gioi_tinh : 'Nam',
                            'SoDienThoai' => $khachHang->so_dien_thoai,
                            'Email' => $khachHang->email,
                            'CCCD' => $khachHang->cmnd_cccd ? $khachHang->cmnd_cccd : '',
                            'NgayCap' => $khachHang->ngay_cap ? $khachHang->ngay_cap : '',
                            'NoiCap' => $khachHang->noi_cap ? $khachHang->noi_cap : '',
                            'DiaChi' => $khachHang->dia_chi ? $khachHang->dia_chi : '',
                            'TrangThai' => $khachHang->trang_thai ? $khachHang->trang_thai : 'Hoạt động'
                        ];
                    })->toArray() : [],
                    'DichVus' => $hopDong->phiDichVus ? $hopDong->phiDichVus->map(function ($dichVu) use ($hopDong) {
                        $pivot = $dichVu->pivot;
                        return [
                            'id' => $dichVu->id,
                            'MaDichVu' => $dichVu->ma_dich_vu,
                            'TenDichVu' => $dichVu->ten_dich_vu,
                            'DonGia' => $dichVu->don_gia,
                            'DonViTinh' => $dichVu->don_vi_tinh,
                            'MaCongTo' => $pivot ? $pivot->ma_cong_to : null,
                            'ChiSoDau' => $pivot ? $pivot->chi_so_dau : null,
                            'NgayTinhPhi' => $pivot ? $pivot->ngay_tinh_phi : null
                        ];
                    })->toArray() : []
                ];
            });
            
            return response()->json($formattedHopDongs);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Xử lý dữ liệu khách hàng và dịch vụ từ frontend
            $khachHangIds = [];
            $dichVuIds = [];
            $maCongTo = [];
            $chiSoDau = [];
            $ngayTinhPhi = [];
            
            // Xử lý khách hàng - hỗ trợ nhiều định dạng dữ liệu từ frontend
            if ($request->has('khach_hang_ids')) {
                $khachHangIds = $request->input('khach_hang_ids');
            } elseif ($request->has('KhachHangs')) {
                $khachHangs = $request->input('KhachHangs');
                if (is_array($khachHangs)) {
                    foreach ($khachHangs as $khachHang) {
                        if (is_array($khachHang)) {
                            $khachHangIds[] = $khachHang['id'] ?? $khachHang['MaKhachHang'] ?? null;
                        } else {
                            $khachHangIds[] = $khachHang;
                        }
                    }
                }
            }
            
            // Xử lý dịch vụ - hỗ trợ nhiều định dạng dữ liệu từ frontend
            if ($request->has('dich_vu_ids')) {
                $dichVuIds = $request->input('dich_vu_ids');
                $maCongTo = $request->input('ma_cong_to', []);
                $chiSoDau = $request->input('chi_so_dau', []);
                $ngayTinhPhi = $request->input('ngay_tinh_phi', []);
            } elseif ($request->has('DichVus')) {
                $dichVus = $request->input('DichVus');
                if (is_array($dichVus)) {
                    foreach ($dichVus as $index => $dichVu) {
                        if (is_array($dichVu)) {
                            $dichVuIds[] = $dichVu['id'] ?? $dichVu['MaDichVu'] ?? null;
                            $maCongTo[] = $dichVu['MaCongTo'] ?? $dichVu['ma_cong_to'] ?? null;
                            $chiSoDau[] = $dichVu['ChiSoDau'] ?? $dichVu['chi_so_dau'] ?? null;
                            $ngayTinhPhi[] = $dichVu['NgayTinhPhi'] ?? $dichVu['ngay_tinh_phi'] ?? null;
                        } else {
                            $dichVuIds[] = $dichVu;
                        }
                    }
                }
            }
            
            // Lọc bỏ các giá trị null hoặc rỗng
            $khachHangIds = array_filter($khachHangIds, function($id) { return !is_null($id) && $id !== ''; });
            $dichVuIds = array_filter($dichVuIds, function($id) { return !is_null($id) && $id !== ''; });
            
            // Chuyển đổi các trường từ camelCase sang snake_case
            $maHopDong = $request->input('MaHopDong') ?? $request->input('ma_hop_dong');
            $phongId = $request->input('MaPhongId') ?? $request->input('phong_id');
            
            // Xử lý các trường ngày tháng
            $ngayBatDau = $this->formatDate($request->input('NgayBatDau') ?? $request->input('ngay_bat_dau'));
            $ngayKetThuc = $this->formatDate($request->input('NgayKetThuc') ?? $request->input('ngay_ket_thuc'));
            $ngayTinhTien = $this->formatDate($request->input('NgayTinhTien') ?? $request->input('ngay_tinh_tien'));
            
            // Các trường khác
            $tienThue = $request->input('TienThue') ?? $request->input('tien_thue');
            $tienCoc = $request->input('TienCoc') ?? $request->input('tien_coc');
            $chuKyThanhToan = $request->input('ChuKyThanhToan') ?? $request->input('chu_ky_thanh_toan');
            $trangThai = $request->input('TrangThai') ?? $request->input('trang_thai') ?? 'Còn hạn';
            
            // Kiểm tra dữ liệu
            if (empty($maHopDong)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã hợp đồng không được để trống'
                ], 422);
            }
            
            if (empty($phongId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã phòng không được để trống'
                ], 422);
            }
            
            // Kiểm tra trùng mã hợp đồng
            if (HopDong::where('ma_hop_dong', $maHopDong)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã hợp đồng đã tồn tại'
                ], 422);
            }
            
            // Tạo hợp đồng
            $hopDong = HopDong::create([
                'ma_hop_dong' => $maHopDong,
                'phong_id' => $phongId,
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' => $ngayKetThuc,
                'tien_thue' => $tienThue,
                'tien_coc' => $tienCoc,
                'chu_ky_thanh_toan' => $chuKyThanhToan,
                'ngay_tinh_tien' => $ngayTinhTien,
                'trang_thai' => $trangThai
            ]);
            
            // Gắn khách hàng vào hợp đồng
            if (!empty($khachHangIds)) {
                $hopDong->khachHangs()->attach($khachHangIds);
            }
            
            // Gắn dịch vụ vào hợp đồng
            $dichVuData = [];
            foreach ($dichVuIds as $index => $dichVuId) {
                $dichVuData[$dichVuId] = [
                    'ma_cong_to' => $maCongTo[$index] ?? null,
                    'chi_so_dau' => $chiSoDau[$index] ?? null,
                    'ngay_tinh_phi' => isset($ngayTinhPhi[$index]) ? $this->formatDate($ngayTinhPhi[$index]) : null
                ];
            }
            if (!empty($dichVuData)) {
                $hopDong->phiDichVus()->attach($dichVuData);
            }
            
            $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
            return response()->json($hopDong, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(HopDong $hopDong): JsonResponse
    {
        $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
        return response()->json($hopDong);
    }

    public function update(Request $request, HopDong $hopDong): JsonResponse
    {
        try {
            // Xử lý dữ liệu khách hàng và dịch vụ từ frontend
            $khachHangIds = [];
            $dichVuIds = [];
            $maCongTo = [];
            $chiSoDau = [];
            $ngayTinhPhi = [];
            
            // Xử lý khách hàng - hỗ trợ nhiều định dạng dữ liệu từ frontend
            if ($request->has('khach_hang_ids')) {
                $khachHangIds = $request->input('khach_hang_ids');
            } elseif ($request->has('KhachHangs')) {
                $khachHangs = $request->input('KhachHangs');
                if (is_array($khachHangs)) {
                    foreach ($khachHangs as $khachHang) {
                        if (is_array($khachHang)) {
                            $khachHangIds[] = $khachHang['id'] ?? $khachHang['MaKhachHang'] ?? null;
                        } else {
                            $khachHangIds[] = $khachHang;
                        }
                    }
                }
            }
            
            // Xử lý dịch vụ - hỗ trợ nhiều định dạng dữ liệu từ frontend
            if ($request->has('dich_vu_ids')) {
                $dichVuIds = $request->input('dich_vu_ids');
                $maCongTo = $request->input('ma_cong_to', []);
                $chiSoDau = $request->input('chi_so_dau', []);
                $ngayTinhPhi = $request->input('ngay_tinh_phi', []);
            } elseif ($request->has('DichVus')) {
                $dichVus = $request->input('DichVus');
                if (is_array($dichVus)) {
                    foreach ($dichVus as $index => $dichVu) {
                        if (is_array($dichVu)) {
                            $dichVuIds[] = $dichVu['id'] ?? $dichVu['MaDichVu'] ?? null;
                            $maCongTo[] = $dichVu['MaCongTo'] ?? $dichVu['ma_cong_to'] ?? null;
                            $chiSoDau[] = $dichVu['ChiSoDau'] ?? $dichVu['chi_so_dau'] ?? null;
                            $ngayTinhPhi[] = $dichVu['NgayTinhPhi'] ?? $dichVu['ngay_tinh_phi'] ?? null;
                        } else {
                            $dichVuIds[] = $dichVu;
                        }
                    }
                }
            }
            
            // Lọc bỏ các giá trị null hoặc rỗng
            $khachHangIds = array_filter($khachHangIds, function($id) { return !is_null($id) && $id !== ''; });
            $dichVuIds = array_filter($dichVuIds, function($id) { return !is_null($id) && $id !== ''; });
            
            // Chuyển đổi các trường từ camelCase sang snake_case
            $phongId = $request->input('MaPhongId') ?? $request->input('phong_id') ?? $hopDong->phong_id;
            
            // Xử lý các trường ngày tháng
            $ngayBatDau = $this->formatDate($request->input('NgayBatDau') ?? $request->input('ngay_bat_dau'));
            $ngayKetThuc = $this->formatDate($request->input('NgayKetThuc') ?? $request->input('ngay_ket_thuc'));
            $ngayTinhTien = $this->formatDate($request->input('NgayTinhTien') ?? $request->input('ngay_tinh_tien'));
            
            // Các trường khác
            $tienThue = $request->input('TienThue') ?? $request->input('tien_thue');
            $tienCoc = $request->input('TienCoc') ?? $request->input('tien_coc');
            $chuKyThanhToan = $request->input('ChuKyThanhToan') ?? $request->input('chu_ky_thanh_toan');
            $trangThai = $request->input('TrangThai') ?? $request->input('trang_thai') ?? $hopDong->trang_thai;
            
            // Kiểm tra dữ liệu
            if (empty($phongId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã phòng không được để trống'
                ], 422);
            }
            
            // Cập nhật hợp đồng
            $hopDong->update([
                'phong_id' => $phongId,
                'ngay_bat_dau' => $ngayBatDau,
                'ngay_ket_thuc' => $ngayKetThuc,
                'tien_thue' => $tienThue,
                'tien_coc' => $tienCoc,
                'chu_ky_thanh_toan' => $chuKyThanhToan,
                'ngay_tinh_tien' => $ngayTinhTien,
                'trang_thai' => $trangThai
            ]);

            // Cập nhật khách hàng trong hợp đồng
            if (!empty($khachHangIds)) {
                $hopDong->khachHangs()->sync($khachHangIds);
            }

            // Cập nhật dịch vụ trong hợp đồng
            $dichVuData = [];
            foreach ($dichVuIds as $index => $dichVuId) {
                $dichVuData[$dichVuId] = [
                    'ma_cong_to' => $maCongTo[$index] ?? null,
                    'chi_so_dau' => $chiSoDau[$index] ?? null,
                    'ngay_tinh_phi' => isset($ngayTinhPhi[$index]) ? $this->formatDate($ngayTinhPhi[$index]) : null
                ];
            }
            if (!empty($dichVuData)) {
                $hopDong->phiDichVus()->sync($dichVuData);
            }

            $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
            return response()->json($hopDong);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(HopDong $hopDong): JsonResponse
    {
        try {
            $hopDong->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa hợp đồng thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa hợp đồng: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Hàm hỗ trợ định dạng ngày tháng
     * 
     * @param string|null $date Ngày tháng cần định dạng
     * @return string|null Ngày tháng đã định dạng hoặc null
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        // Xử lý các định dạng ngày từ frontend
        try {
            // Nếu là định dạng DD/MM/YYYY
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date)) {
                $parts = explode('/', $date);
                return $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            }
            
            // Nếu đã là định dạng YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $date)) {
                return $date;
            }
            
            // Thử chuyển đổi bằng strtotime
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            // Ghi log lỗi nếu cần
        }
        
        // Trả về nguyên bản nếu không xử lý được
        return $date;
    }
}
