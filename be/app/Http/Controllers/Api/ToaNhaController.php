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
            $toaNhas = ToaNha::with('chuToaNha')->paginate(10);
            return response()->json([
                'status' => 'success',
                'data' => $toaNhas
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách tòa nhà'
            ], 500);
        }
    }

    public function store(ToaNhaRequest $request)
    {
        try {
            DB::beginTransaction();

            $toaNha = ToaNha::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo tòa nhà thành công',
                'data' => $toaNha
            ], 201);
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
            $toaNha = ToaNha::with('chuToaNha', 'phongs')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $toaNha
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin tòa nhà: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy tòa nhà'
            ], 404);
        }
    }

    public function update(ToaNhaRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $toaNha = ToaNha::findOrFail($id);
            $toaNha->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật tòa nhà thành công',
                'data' => $toaNha
            ]);
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
