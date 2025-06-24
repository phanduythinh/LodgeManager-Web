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

    /**
     * Lấy danh sách phòng theo tòa nhà
     *
     * @param mixed $id ID hoặc MaNha của tòa nhà
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPhongs($id)
    {
        try {
            // Ghi log ID để debug
            Log::info('getPhongs được gọi với ID/MaNha: ' . $id);

            // Kiểm tra ID có hợp lệ không
            if (!$id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID tòa nhà không hợp lệ'
                ], 400);
            }

            // Tìm tòa nhà theo ID hoặc MaNha
            $toaNha = null;

            // Nếu là số, thử tìm theo ID trước
            if (is_numeric($id)) {
                $toaNha = ToaNha::find($id);
            }

            // Nếu không tìm thấy theo ID, thử tìm theo MaNha
            if (!$toaNha) {
                $toaNha = ToaNha::where('ma_nha', $id)->first();
            }

            // Kiểm tra tòa nhà có tồn tại không
            if (!$toaNha) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy tòa nhà với ID/MaNha: ' . $id
                ], 404);
            }

            // Lấy danh sách phòng của tòa nhà với eager loading
            $phongs = $toaNha->phongs()->with('toaNha')->get();

            // Kiểm tra danh sách phòng có trống không
            if ($phongs->isEmpty()) {
                return response()->json([]);
            }

            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedPhongs = $phongs->map(function ($phong) use ($toaNha) {
                try {
                    return [
                        'id' => $phong->id,
                        'MaPhong' => $phong->ma_phong,
                        'TenPhong' => $phong->ten_phong,
                        'Tang' => $phong->tang ?? 0,
                        'DienTich' => $phong->dien_tich ?? 0,
                        'GiaThue' => $phong->gia_thue ?? 0,
                        'DatCoc' => $phong->dat_coc ?? 0,
                        'SoKhachToiDa' => $phong->so_khach_toi_da ?? 0,
                        'TrangThai' => $phong->trang_thai ?? 'Trống',
                        'ToaNhaId' => $phong->toa_nha_id,
                        'MaNhaId' => $toaNha->id,
                        'MaNha' => $toaNha->ma_nha,
                        'TenNha' => $toaNha->ten_nha
                    ];
                } catch (\Exception $e) {
                    Log::error('Lỗi khi xử lý phòng: ' . $e->getMessage());
                    return null;
                }
            })->filter()->values();

            return response()->json($formattedPhongs);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách phòng theo tòa nhà: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách phòng theo tòa nhà: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = ToaNha::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('dia_chi', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $toaNhas = $query->with('chuToaNha')->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $toaNhas
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm tòa nhà'
            ], 500);
        }
    }
}
