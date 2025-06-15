<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhiDichVuRequest;
use App\Models\PhiDichVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhiDichVuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $phiDichVus = PhiDichVu::with('toaNha')->get();
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedPhiDichVus = $phiDichVus->map(function ($phiDichVu) {
                return [
                    'id' => $phiDichVu->id,
                    'MaDichVu' => $phiDichVu->ma_dich_vu,
                    'TenDichVu' => $phiDichVu->ten_dich_vu,
                    'LoaiDichVu' => $phiDichVu->loai_dich_vu ?? '',
                    'DonGia' => $phiDichVu->don_gia ?? 0,
                    'DonViTinh' => $phiDichVu->don_vi_tinh ?? '',
                    'MaNhaId' => $phiDichVu->toa_nha_id,
                    'ToaNhaId' => $phiDichVu->toa_nha_id,
                    'MaNha' => $phiDichVu->toaNha ? $phiDichVu->toaNha->ma_nha : '',
                    'TenNha' => $phiDichVu->toaNha ? $phiDichVu->toaNha->ten_nha : '',
                    'TrangThai' => $phiDichVu->trang_thai ?? 'Hoạt động'
                ];
            });
            
            return response()->json($formattedPhiDichVus);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách phí dịch vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(PhiDichVuRequest $request)
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
            $phiDichVuData = [
                'ma_dich_vu' => $request->MaDichVu,
                'ten_dich_vu' => $request->TenDichVu,
                'loai_dich_vu' => $request->LoaiDichVu,
                'don_gia' => $request->DonGia,
                'don_vi_tinh' => $request->DonViTinh,
                'toa_nha_id' => $toaNha->id
            ];
            
            $phiDichVu = PhiDichVu::create($phiDichVuData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo phí dịch vụ thành công',
                'data' => $phiDichVu
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo phí dịch vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $phiDichVu = PhiDichVu::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $phiDichVu
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phí dịch vụ'
            ], 404);
        }
    }

    public function update(PhiDichVuRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $phiDichVu = PhiDichVu::findOrFail($id);

            // Kiểm tra và lấy ID tòa nhà
            if ($request->has('TenNha')) {
                $toaNha = DB::table('toa_nhas')->where('ten_nha', $request->TenNha)->first();
                if (!$toaNha) {
                    return response()->json(['status' => 'error', 'message' => 'Tòa nhà không tồn tại'], 422);
                }
                $phiDichVu->toa_nha_id = $toaNha->id;
            } else if ($request->has('ToaNhaId')) {
                $phiDichVu->toa_nha_id = $request->ToaNhaId;
            }

            // Cập nhật các trường khác
            $phiDichVu->ma_dich_vu = $request->MaDichVu;
            $phiDichVu->ten_dich_vu = $request->TenDichVu;
            $phiDichVu->loai_dich_vu = $request->LoaiDichVu;
            $phiDichVu->don_gia = $request->DonGia;
            $phiDichVu->don_vi_tinh = $request->DonViTinh;
            
            $phiDichVu->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật phí dịch vụ thành công',
                'data' => $phiDichVu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật phí dịch vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $phiDichVu = PhiDichVu::findOrFail($id);

            if ($phiDichVu->chiTietHoaDons()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa phí dịch vụ đang được sử dụng trong hóa đơn'
                ], 400);
            }

            $phiDichVu->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa phí dịch vụ thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa phí dịch vụ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = PhiDichVu::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('mo_ta', 'like', "%{$keyword}%")
                        ->orWhere('loai_dich_vu', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('loai_dich_vu')) {
                $query->where('loai_dich_vu', $request->loai_dich_vu);
            }

            $phiDichVus = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $phiDichVus
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm phí dịch vụ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm phí dịch vụ'
            ], 500);
        }
    }
}
