<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhongRequest;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhongController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $phongs = Phong::with(['toaNha', 'hopDongs'])->get();
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedPhongs = $phongs->map(function ($phong) {
                return [
                    'id' => $phong->id,
                    'MaPhong' => $phong->ma_phong,
                    'TenPhong' => $phong->ten_phong,
                    'Tang' => $phong->tang,
                    'GiaThue' => $phong->gia_thue,
                    'DatCoc' => $phong->dat_coc,
                    'DienTich' => $phong->dien_tich,
                    'SoKhachToiDa' => $phong->so_khach_toi_da,
                    'TrangThai' => $phong->trang_thai,
                    'MaNhaId' => $phong->toa_nha_id,
                    'TenNha' => $phong->toaNha ? $phong->toaNha->ten_nha : '',
                    'HopDongs' => $phong->hopDongs ? $phong->hopDongs->map(function ($hopDong) {
                        return $hopDong->only(['id', 'ma_hop_dong', 'ngay_bat_dau', 'ngay_ket_thuc', 'trang_thai']);
                    })->toArray() : []
                ];
            });
            
            return response()->json($formattedPhongs);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách phòng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(PhongRequest $request)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo phòng thành công',
                'data' => $phong
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo phòng'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $phong = Phong::with(['toaNha', 'hopDong'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $phong
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phòng'
            ], 404);
        }
    }

    public function update(PhongRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::findOrFail($id);

            // Kiểm tra nếu phòng đang có hợp đồng
            if ($phong->hopDong && $phong->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật phòng đang có hợp đồng thuê'
                ], 400);
            }

            $phong->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật phòng thành công',
                'data' => $phong
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật phòng'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $phong = Phong::findOrFail($id);

            // Kiểm tra nếu phòng đang có hợp đồng
            if ($phong->hopDong && $phong->hopDong->trang_thai === 'dang_thue') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa phòng đang có hợp đồng thuê'
                ], 400);
            }

            $phong->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa phòng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa phòng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa phòng'
            ], 500);
        }
    }
}
