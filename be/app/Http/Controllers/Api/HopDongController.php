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
        $hopDongs = HopDong::with(['phong.toaNha', 'khachHangs', 'phiDichVus'])->get();
        return response()->json($hopDongs);
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
