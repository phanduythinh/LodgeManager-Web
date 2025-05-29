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
                    'Tang' => $phong->tang ?? 0,
                    'GiaThue' => $phong->gia_thue ?? 0,
                    'DatCoc' => $phong->dat_coc ?? 0,
                    'DienTich' => $phong->dien_tich ?? 0,
                    'SoKhachToiDa' => $phong->so_khach_toi_da ?? 0,
                    'TrangThai' => $phong->trang_thai ?? 'Trống',
                    'ToaNhaId' => $phong->toa_nha_id,
                    'MaNhaId' => $phong->toa_nha_id,
                    'MaNha' => $phong->toaNha ? $phong->toaNha->ma_nha : '',
                    'TenNha' => $phong->toaNha ? $phong->toaNha->ten_nha : '',
                    'HopDongs' => $phong->hopDongs ? $phong->hopDongs->map(function ($hopDong) {
                        return [
                            'id' => $hopDong->id,
                            'MaHopDong' => $hopDong->ma_hop_dong,
                            'NgayBatDau' => $hopDong->ngay_bat_dau,
                            'NgayKetThuc' => $hopDong->ngay_ket_thuc,
                            'TrangThai' => $hopDong->trang_thai
                        ];
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
            
            // Lấy ID của tòa nhà dựa trên TenNha
            $toaNha = DB::table('toa_nhas')->where('ten_nha', $request->TenNha)->first();
            
            if (!$toaNha) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy tòa nhà với tên: ' . $request->TenNha
                ], 422);
            }
            
            // Chuyển đổi dữ liệu từ frontend sang định dạng database
            $phongData = [
                'ma_phong' => $request->MaPhong,
                'ten_phong' => $request->TenPhong,
                'tang' => $request->Tang,
                'gia_thue' => $request->GiaThue,
                'dat_coc' => $request->DatCoc,
                'dien_tich' => $request->DienTich,
                'so_khach_toi_da' => $request->SoKhachToiDa,
                'trang_thai' => $request->TrangThai,
                'toa_nha_id' => $toaNha->id
            ];
            
            $phong = Phong::create($phongData);

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
                'message' => 'Có lỗi xảy ra khi tạo phòng: ' . $e->getMessage()
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

            // Tìm phòng theo ID hoặc MaPhong
            $phong = is_numeric($id) ? Phong::findOrFail($id) : Phong::where('ma_phong', $id)->firstOrFail();

            // Kiểm tra nếu phòng đang có hợp đồng
            if ($phong->hopDongs()->where('trang_thai', 'dang_thue')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật phòng đang có hợp đồng thuê'
                ], 400);
            }
            
            // Lấy ID của tòa nhà dựa trên TenNha
            $toaNha = DB::table('toa_nhas')->where('ten_nha', $request->TenNha)->first();
            
            if (!$toaNha) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy tòa nhà với tên: ' . $request->TenNha
                ], 422);
            }
            
            // Chuyển đổi dữ liệu từ frontend sang định dạng database
            $phongData = [
                'ma_phong' => $request->MaPhong,
                'ten_phong' => $request->TenPhong,
                'tang' => $request->Tang,
                'gia_thue' => $request->GiaThue,
                'dat_coc' => $request->DatCoc,
                'dien_tich' => $request->DienTich,
                'so_khach_toi_da' => $request->SoKhachToiDa,
                'trang_thai' => $request->TrangThai,
                'toa_nha_id' => $toaNha->id
            ];

            $phong->update($phongData);

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
                'message' => 'Có lỗi xảy ra khi cập nhật phòng: ' . $e->getMessage()
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
