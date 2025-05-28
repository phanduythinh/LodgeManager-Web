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
                    'MaNhaId' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->id : null,
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
                            'SoDienThoai' => $khachHang->so_dien_thoai,
                            'Email' => $khachHang->email,
                            'CMND_CCCD' => $khachHang->cmnd_cccd
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
        $validated = $request->validate([
            'ma_hop_dong' => 'required|unique:hop_dongs',
            'phong_id' => 'required|exists:phongs,id',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'tien_thue' => 'required|numeric',
            'tien_coc' => 'required|numeric',
            'chu_ky_thanh_toan' => 'required',
            'ngay_tinh_tien' => 'required|date',
            'trang_thai' => 'required',
            'khach_hang_ids' => 'required|array',
            'khach_hang_ids.*' => 'exists:khach_hangs,id',
            'dich_vu_ids' => 'required|array',
            'dich_vu_ids.*' => 'exists:phi_dich_vus,id',
            'ma_cong_to' => 'nullable|array',
            'ma_cong_to.*' => 'nullable|string',
            'chi_so_dau' => 'nullable|array',
            'chi_so_dau.*' => 'nullable|numeric',
            'ngay_tinh_phi' => 'nullable|array',
            'ngay_tinh_phi.*' => 'nullable|date'
        ]);

        $hopDong = HopDong::create([
            'ma_hop_dong' => $validated['ma_hop_dong'],
            'phong_id' => $validated['phong_id'],
            'ngay_bat_dau' => $validated['ngay_bat_dau'],
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'],
            'tien_thue' => $validated['tien_thue'],
            'tien_coc' => $validated['tien_coc'],
            'chu_ky_thanh_toan' => $validated['chu_ky_thanh_toan'],
            'ngay_tinh_tien' => $validated['ngay_tinh_tien'],
            'trang_thai' => $validated['trang_thai']
        ]);

        $hopDong->khachHangs()->attach($validated['khach_hang_ids']);

        $dichVuData = [];
        foreach ($validated['dich_vu_ids'] as $index => $dichVuId) {
            $dichVuData[$dichVuId] = [
                'ma_cong_to' => $validated['ma_cong_to'][$index] ?? null,
                'chi_so_dau' => $validated['chi_so_dau'][$index] ?? null,
                'ngay_tinh_phi' => $validated['ngay_tinh_phi'][$index] ?? null
            ];
        }
        $hopDong->phiDichVus()->attach($dichVuData);

        $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
        return response()->json($hopDong, 201);
    }

    public function show(HopDong $hopDong): JsonResponse
    {
        $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
        return response()->json($hopDong);
    }

    public function update(Request $request, HopDong $hopDong): JsonResponse
    {
        $validated = $request->validate([
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'tien_thue' => 'required|numeric',
            'tien_coc' => 'required|numeric',
            'chu_ky_thanh_toan' => 'required',
            'ngay_tinh_tien' => 'required|date',
            'trang_thai' => 'required',
            'khach_hang_ids' => 'required|array',
            'khach_hang_ids.*' => 'exists:khach_hangs,id',
            'dich_vu_ids' => 'required|array',
            'dich_vu_ids.*' => 'exists:phi_dich_vus,id',
            'ma_cong_to' => 'nullable|array',
            'ma_cong_to.*' => 'nullable|string',
            'chi_so_dau' => 'nullable|array',
            'chi_so_dau.*' => 'nullable|numeric',
            'ngay_tinh_phi' => 'nullable|array',
            'ngay_tinh_phi.*' => 'nullable|date'
        ]);

        $hopDong->update([
            'ngay_bat_dau' => $validated['ngay_bat_dau'],
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'],
            'tien_thue' => $validated['tien_thue'],
            'tien_coc' => $validated['tien_coc'],
            'chu_ky_thanh_toan' => $validated['chu_ky_thanh_toan'],
            'ngay_tinh_tien' => $validated['ngay_tinh_tien'],
            'trang_thai' => $validated['trang_thai']
        ]);

        $hopDong->khachHangs()->sync($validated['khach_hang_ids']);

        $dichVuData = [];
        foreach ($validated['dich_vu_ids'] as $index => $dichVuId) {
            $dichVuData[$dichVuId] = [
                'ma_cong_to' => $validated['ma_cong_to'][$index] ?? null,
                'chi_so_dau' => $validated['chi_so_dau'][$index] ?? null,
                'ngay_tinh_phi' => $validated['ngay_tinh_phi'][$index] ?? null
            ];
        }
        $hopDong->phiDichVus()->sync($dichVuData);

        $hopDong->load(['phong.toaNha', 'khachHangs', 'phiDichVus']);
        return response()->json($hopDong);
    }

    public function destroy(HopDong $hopDong): JsonResponse
    {
        $hopDong->delete();
        return response()->json(null, 204);
    }
}
