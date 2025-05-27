<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HoaDonRequest;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HoaDonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = HoaDon::query();

            if ($request->has('hop_dong_id')) {
                $query->where('hop_dong_id', $request->hop_dong_id);
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('tu_ngay')) {
                $query->where('ngay_tao', '>=', $request->tu_ngay);
            }

            if ($request->has('den_ngay')) {
                $query->where('ngay_tao', '<=', $request->den_ngay);
            }

            $hoaDons = $query->with(['hopDong', 'chiTietDichVu'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $hoaDons
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách hóa đơn'
            ], 500);
        }
    }

    public function store(HoaDonRequest $request)
    {
        try {
            DB::beginTransaction();

            $hoaDon = HoaDon::create($request->validated());

            // Tạo chi tiết dịch vụ
            foreach ($request->chi_tiet_dich_vu as $chiTiet) {
                $hoaDon->chiTietDichVu()->create($chiTiet);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo hóa đơn thành công',
                'data' => $hoaDon->load('chiTietDichVu')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo hóa đơn'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $hoaDon = HoaDon::with(['hopDong', 'chiTietDichVu'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $hoaDon
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }
    }

    public function update(HoaDonRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $hoaDon = HoaDon::findOrFail($id);

            // Kiểm tra nếu hóa đơn đã thanh toán
            if ($hoaDon->trang_thai === 'da_thanh_toan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể cập nhật hóa đơn đã thanh toán'
                ], 400);
            }

            $hoaDon->update($request->validated());

            // Cập nhật chi tiết dịch vụ
            $hoaDon->chiTietDichVu()->delete();
            foreach ($request->chi_tiet_dich_vu as $chiTiet) {
                $hoaDon->chiTietDichVu()->create($chiTiet);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật hóa đơn thành công',
                'data' => $hoaDon->load('chiTietDichVu')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật hóa đơn'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $hoaDon = HoaDon::findOrFail($id);

            // Kiểm tra nếu hóa đơn đã thanh toán
            if ($hoaDon->trang_thai === 'da_thanh_toan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa hóa đơn đã thanh toán'
                ], 400);
            }

            $hoaDon->chiTietDichVu()->delete();
            $hoaDon->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa hóa đơn thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa hóa đơn'
            ], 500);
        }
    }

    public function thanhToan($id)
    {
        try {
            DB::beginTransaction();

            $hoaDon = HoaDon::findOrFail($id);

            // Kiểm tra nếu hóa đơn đã thanh toán
            if ($hoaDon->trang_thai === 'da_thanh_toan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hóa đơn đã được thanh toán'
                ], 400);
            }

            $hoaDon->update([
                'trang_thai' => 'da_thanh_toan',
                'ngay_thanh_toan' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Thanh toán hóa đơn thành công',
                'data' => $hoaDon
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi thanh toán hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi thanh toán hóa đơn'
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = HoaDon::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_hoa_don', 'like', "%{$keyword}%")
                        ->orWhereHas('hopDong', function ($q) use ($keyword) {
                            $q->where('ma_hop_dong', 'like', "%{$keyword}%")
                                ->orWhereHas('khachHang', function ($q) use ($keyword) {
                                    $q->where('ho_ten', 'like', "%{$keyword}%")
                                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%");
                                });
                        });
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('tu_ngay')) {
                $query->where('ngay_tao', '>=', $request->tu_ngay);
            }

            if ($request->has('den_ngay')) {
                $query->where('ngay_tao', '<=', $request->den_ngay);
            }

            $hoaDons = $query->with(['hopDong', 'chiTietDichVu'])->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $hoaDons
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm hóa đơn: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm hóa đơn'
            ], 500);
        }
    }
}
