<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToaNhaRequest;
use App\Models\ToaNha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ToaNhaController extends Controller
{
    public function index()
    {
        try {
            $toaNhas = ToaNha::with(['phongs', 'phiDichVus'])->get();
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedToaNhas = $toaNhas->map(function ($toaNha) {
                return [
                    'id' => $toaNha->id,
                    'MaNha' => $toaNha->ma_nha,
                    'TenNha' => $toaNha->ten_nha,
                    'DiaChiNha' => $toaNha->dia_chi_nha,
                    'XaPhuong' => $toaNha->xa_phuong,
                    'QuanHuyen' => $toaNha->quan_huyen,
                    'TinhThanh' => $toaNha->tinh_thanh,
                    'TrangThai' => $toaNha->trang_thai,
                    'Phongs' => $toaNha->phongs->map(function ($phong) {
                        return $phong->only(['id', 'ma_phong', 'ten_phong', 'trang_thai']);
                    })->toArray(),
                    'PhiDicuVus' => $toaNha->phiDichVus->map(function ($phi) {
                        return $phi->only(['id', 'ten_phi', 'gia_phi', 'trang_thai']);
                    })->toArray(),
                    'SoPhong' => $toaNha->phongs->count()
                ];
            });
            
            return response()->json($formattedToaNhas);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách tòa nhà'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra xem mã nhà đã tồn tại chưa
            $existingToaNha = ToaNha::where('ma_nha', $request->input('MaNha'))->first();
            if ($existingToaNha) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mã tòa nhà đã tồn tại'
                ], 400);
            }

            // Chuyển đổi dữ liệu từ frontend sang backend
            $toaNhaData = [
                'ma_nha' => $request->input('MaNha'),
                'ten_nha' => $request->input('TenNha'),
                'dia_chi_nha' => $request->input('DiaChiNha'),
                'xa_phuong' => $request->input('XaPhuong'),
                'quan_huyen' => $request->input('QuanHuyen'),
                'tinh_thanh' => $request->input('TinhThanh'),
                'trang_thai' => $request->input('TrangThai', 'Không hoạt động')
            ];

            $toaNha = ToaNha::create($toaNhaData);

            DB::commit();

            // Chuyển đổi dữ liệu trả về cho frontend
            $formattedToaNha = [
                'id' => $toaNha->id,
                'MaNha' => $toaNha->ma_nha,
                'TenNha' => $toaNha->ten_nha,
                'DiaChiNha' => $toaNha->dia_chi_nha,
                'XaPhuong' => $toaNha->xa_phuong,
                'QuanHuyen' => $toaNha->quan_huyen,
                'TinhThanh' => $toaNha->tinh_thanh,
                'TrangThai' => $toaNha->trang_thai,
                'Phongs' => [],
                'PhiDicuVus' => [],
                'SoPhong' => 0
            ];

            return response()->json($formattedToaNha, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo tòa nhà'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $toaNha = ToaNha::with(['phongs', 'phiDichVus'])->findOrFail($id);
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedToaNha = [
                'id' => $toaNha->id,
                'MaNha' => $toaNha->ma_nha,
                'TenNha' => $toaNha->ten_nha,
                'DiaChiNha' => $toaNha->dia_chi_nha,
                'XaPhuong' => $toaNha->xa_phuong,
                'QuanHuyen' => $toaNha->quan_huyen,
                'TinhThanh' => $toaNha->tinh_thanh,
                'TrangThai' => $toaNha->trang_thai,
                'Phongs' => $toaNha->phongs->map(function ($phong) {
                    return $phong->only(['id', 'ma_phong', 'ten_phong', 'trang_thai']);
                })->toArray(),
                'PhiDicuVus' => $toaNha->phiDichVus->map(function ($phi) {
                    return $phi->only(['id', 'ten_phi', 'gia_phi', 'trang_thai']);
                })->toArray(),
                'SoPhong' => $toaNha->phongs->count()
            ];
            
            return response()->json($formattedToaNha);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy tòa nhà'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $toaNha = ToaNha::findOrFail($id);
            
            // Chuyển đổi dữ liệu từ frontend sang backend
            $toaNhaData = [
                'ten_nha' => $request->input('TenNha'),
                'dia_chi_nha' => $request->input('DiaChiNha'),
                'xa_phuong' => $request->input('XaPhuong'),
                'quan_huyen' => $request->input('QuanHuyen'),
                'tinh_thanh' => $request->input('TinhThanh'),
                'trang_thai' => $request->input('TrangThai')
            ];

            $toaNha->update($toaNhaData);

            DB::commit();

            // Lấy lại tòa nhà với các mối quan hệ
            $toaNha = ToaNha::with(['phongs', 'phiDichVus'])->findOrFail($id);
            
            // Chuyển đổi dữ liệu trả về cho frontend
            $formattedToaNha = [
                'id' => $toaNha->id,
                'MaNha' => $toaNha->ma_nha,
                'TenNha' => $toaNha->ten_nha,
                'DiaChiNha' => $toaNha->dia_chi_nha,
                'XaPhuong' => $toaNha->xa_phuong,
                'QuanHuyen' => $toaNha->quan_huyen,
                'TinhThanh' => $toaNha->tinh_thanh,
                'TrangThai' => $toaNha->trang_thai,
                'Phongs' => $toaNha->phongs->map(function ($phong) {
                    return $phong->only(['id', 'ma_phong', 'ten_phong', 'trang_thai']);
                })->toArray(),
                'PhiDicuVus' => $toaNha->phiDichVus->map(function ($phi) {
                    return $phi->only(['id', 'ten_phi', 'gia_phi', 'trang_thai']);
                })->toArray(),
                'SoPhong' => $toaNha->phongs->count()
            ];

            return response()->json($formattedToaNha);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật tòa nhà'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $toaNha = ToaNha::findOrFail($id);

            // Kiểm tra xem tòa nhà có phòng nào không
            if ($toaNha->phongs()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa tòa nhà vì còn phòng'
                ], 400);
            }

            $toaNha->delete();

            DB::commit();

            return response()->json([
                'id' => $id,
                'status' => 'success',
                'message' => 'Xóa tòa nhà thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa tòa nhà'
            ], 500);
        }
    }
}
