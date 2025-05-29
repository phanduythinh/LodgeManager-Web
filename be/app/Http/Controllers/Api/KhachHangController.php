<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KhachHangRequest;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KhachHangController extends Controller
{
    public function index()
    {
        try {
            $khachHangs = KhachHang::with(['hopDongs', 'hopDongs.phong', 'hopDongs.phong.toaNha'])->get();
            
            // Chuyển đổi dữ liệu để phù hợp với cấu trúc frontend
            $formattedKhachHangs = $khachHangs->map(function ($khachHang) {
                return [
                    'id' => $khachHang->id,
                    'MaKhachHang' => $khachHang->ma_khach_hang,
                    'HoTen' => $khachHang->ho_ten,
                    'NgaySinh' => $khachHang->ngay_sinh,
                    'GioiTinh' => $khachHang->gioi_tinh ? $khachHang->gioi_tinh : 'Nam',
                    'SoDienThoai' => $khachHang->so_dien_thoai,
                    'Email' => $khachHang->email,
                    // Trả về cả hai trường để đảm bảo tương thích
                    'CCCD' => $khachHang->cccd ? $khachHang->cccd : '',
                    'CMND_CCCD' => $khachHang->cccd ? $khachHang->cccd : '',
                    'NgayCap' => $khachHang->ngay_cap ? $khachHang->ngay_cap : '',
                    'NoiCap' => $khachHang->noi_cap ? $khachHang->noi_cap : '',
                    // Trả về địa chỉ chi tiết
                    'DiaChiNha' => $khachHang->dia_chi_nha ? $khachHang->dia_chi_nha : '',
                    'XaPhuong' => $khachHang->xa_phuong ? $khachHang->xa_phuong : '',
                    'QuanHuyen' => $khachHang->quan_huyen ? $khachHang->quan_huyen : '',
                    'TinhThanh' => $khachHang->tinh_thanh ? $khachHang->tinh_thanh : '',
                    // Giữ lại trường DiaChi để tương thích ngược
                    'DiaChi' => $khachHang->dia_chi_nha ? $khachHang->dia_chi_nha : '',
                    'TrangThai' => $khachHang->trang_thai ? $khachHang->trang_thai : 'Hoạt động',
                    'HopDongs' => $khachHang->hopDongs ? $khachHang->hopDongs->map(function ($hopDong) {
                        return [
                            'id' => $hopDong->id,
                            'MaHopDong' => $hopDong->ma_hop_dong,
                            'TrangThai' => $hopDong->trang_thai,
                            'TenPhong' => $hopDong->phong ? $hopDong->phong->ten_phong : '',
                            'TenNha' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ten_nha : ''
                        ];
                    })->toArray() : []
                ];
            });
            
            return response()->json($formattedKhachHangs);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(KhachHangRequest $request)
    {
        try {
            DB::beginTransaction();

            // Xử lý dữ liệu đầu vào từ frontend
            $data = $request->validated();
            
            // Đảm bảo các trường bắt buộc có giá trị
            if (!isset($data['ma_khach_hang']) || empty($data['ma_khach_hang'])) {
                // Tạo mã khách hàng tự động nếu không có
                $data['ma_khach_hang'] = 'KH' . time();
            }
            
            // Đảm bảo các trường có giá trị mặc định
            $data['gioi_tinh'] = $data['gioi_tinh'] ?? 'nam';

            $khachHang = KhachHang::create($data);

            DB::commit();
            
            // Trả về dữ liệu đã được định dạng để phù hợp với frontend
            return response()->json([
                'id' => $khachHang->id,
                'MaKhachHang' => $khachHang->ma_khach_hang,
                'HoTen' => $khachHang->ho_ten,
                'NgaySinh' => $khachHang->ngay_sinh,
                'GioiTinh' => $khachHang->gioi_tinh,
                'SoDienThoai' => $khachHang->so_dien_thoai,
                'Email' => $khachHang->email,
                'CCCD' => $khachHang->cccd,
                'DiaChiNha' => $khachHang->dia_chi_nha,
                'XaPhuong' => $khachHang->xa_phuong,
                'QuanHuyen' => $khachHang->quan_huyen,
                'TinhThanh' => $khachHang->tinh_thanh,
                'HopDongs' => []
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $khachHang = KhachHang::with(['hopDongs', 'hopDongs.phong', 'hopDongs.phong.toaNha'])->findOrFail($id);
            
            // Định dạng dữ liệu trả về để phù hợp với frontend
            $formattedData = [
                'id' => $khachHang->id,
                'MaKhachHang' => $khachHang->ma_khach_hang,
                'HoTen' => $khachHang->ho_ten,
                'NgaySinh' => $khachHang->ngay_sinh,
                'GioiTinh' => $khachHang->gioi_tinh ? $khachHang->gioi_tinh : 'Nam',
                'SoDienThoai' => $khachHang->so_dien_thoai,
                'Email' => $khachHang->email,
                'CMND_CCCD' => $khachHang->cmnd_cccd ? $khachHang->cmnd_cccd : '',
                'NgayCap' => $khachHang->ngay_cap ? $khachHang->ngay_cap : '',
                'NoiCap' => $khachHang->noi_cap ? $khachHang->noi_cap : '',
                'DiaChi' => $khachHang->dia_chi ? $khachHang->dia_chi : '',
                'TrangThai' => $khachHang->trang_thai ? $khachHang->trang_thai : 'Hoạt động',
                'HopDongs' => $khachHang->hopDongs ? $khachHang->hopDongs->map(function ($hopDong) {
                    return [
                        'id' => $hopDong->id,
                        'MaHopDong' => $hopDong->ma_hop_dong,
                        'TrangThai' => $hopDong->trang_thai,
                        'TenPhong' => $hopDong->phong ? $hopDong->phong->ten_phong : '',
                        'TenNha' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ten_nha : ''
                    ];
                })->toArray() : []
            ];
            
            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
    }

    public function update(KhachHangRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Log thông tin ID để debug
            Log::info('Cập nhật khách hàng với ID: ' . $id);
            Log::info('Dữ liệu nhận được: ' . json_encode($request->all()));
            
            // Kiểm tra xem khách hàng có tồn tại không
            $khachHang = KhachHang::where('id', $id)->first();
            
            if (!$khachHang) {
                // Nếu không tìm thấy bằng ID, thử tìm bằng mã khách hàng
                $khachHang = KhachHang::where('ma_khach_hang', $id)->first();
                
                if (!$khachHang) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy khách hàng với ID hoặc mã: ' . $id
                    ], 404);
                }
            }
            
            // Xử lý dữ liệu đầu vào từ frontend
            $data = $request->validated();
            
            // Đảm bảo các trường có giá trị mặc định
            $data['gioi_tinh'] = $data['gioi_tinh'] ?? 'nam';

            // Cập nhật khách hàng
            $khachHang->update($data);

            // Lấy lại khách hàng với các mối quan hệ
            $khachHang = $khachHang->fresh(['hopDongs', 'hopDongs.phong', 'hopDongs.phong.toaNha']);

            DB::commit();
            
            // Trả về dữ liệu theo định dạng của frontend
            return response()->json([
                'id' => $khachHang->id,
                'MaKhachHang' => $khachHang->ma_khach_hang,
                'HoTen' => $khachHang->ho_ten,
                'NgaySinh' => $khachHang->ngay_sinh,
                'GioiTinh' => $khachHang->gioi_tinh,
                'SoDienThoai' => $khachHang->so_dien_thoai,
                'Email' => $khachHang->email,
                'CCCD' => $khachHang->cccd,
                'DiaChiNha' => $khachHang->dia_chi_nha,
                'XaPhuong' => $khachHang->xa_phuong,
                'QuanHuyen' => $khachHang->quan_huyen,
                'TinhThanh' => $khachHang->tinh_thanh,
                'HopDongs' => $khachHang->hopDongs ? $khachHang->hopDongs->map(function ($hopDong) {
                    return [
                        'id' => $hopDong->id,
                        'MaHopDong' => $hopDong->ma_hop_dong,
                        'TrangThai' => $hopDong->trang_thai,
                        'TenPhong' => $hopDong->phong ? $hopDong->phong->ten_phong : '',
                        'TenNha' => $hopDong->phong && $hopDong->phong->toaNha ? $hopDong->phong->toaNha->ten_nha : ''
                    ];
                })->toArray() : []
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $khachHang = KhachHang::findOrFail($id);

            // Kiểm tra xem khách hàng có hợp đồng nào không
            if ($khachHang->hopDongs()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa khách hàng vì còn hợp đồng'
                ], 400);
            }

            $khachHang->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa khách hàng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa khách hàng'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = KhachHang::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%")
                        ->orWhere('cmnd_cccd', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $khachHangs = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $khachHangs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm khách hàng'
            ], 500);
        }
    }
}
