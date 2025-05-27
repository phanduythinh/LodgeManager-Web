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
            $khachHangs = KhachHang::with('hopDongs')->paginate(10);
            return response()->json([
                'status' => 'success',
                'data' => $khachHangs
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách khách hàng'
            ], 500);
        }
    }

    public function store(KhachHangRequest $request)
    {
        try {
            DB::beginTransaction();

            $khachHang = KhachHang::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo khách hàng thành công',
                'data' => $khachHang
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo khách hàng'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $khachHang = KhachHang::with(['hopDongs', 'hopDongs.phong', 'hopDongs.phong.toaNha'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $khachHang
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

            $khachHang = KhachHang::findOrFail($id);
            $khachHang->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật khách hàng thành công',
                'data' => $khachHang
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật khách hàng: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật khách hàng'
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
