<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChuNhaRequest;
use App\Http\Resources\ChuNhaResource;
use App\Models\ChuNha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChuNhaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ChuNha::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('cmnd', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $chuNhas = $query->paginate(10);
            return ChuNhaResource::collection($chuNhas);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách chủ nhà'], 500);
        }
    }

    public function store(ChuNhaRequest $request)
    {
        try {
            $chuNha = ChuNha::create($request->validated());
            return new ChuNhaResource($chuNha);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tạo chủ nhà'], 500);
        }
    }

    public function show($id)
    {
        try {
            $chuNha = ChuNha::findOrFail($id);
            return new ChuNhaResource($chuNha);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Không tìm thấy chủ nhà'], 404);
        }
    }

    public function update(ChuNhaRequest $request, $id)
    {
        try {
            $chuNha = ChuNha::findOrFail($id);
            $chuNha->update($request->validated());
            return new ChuNhaResource($chuNha);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật chủ nhà'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $chuNha = ChuNha::findOrFail($id);

            // Kiểm tra xem chủ nhà có đang được sử dụng trong tòa nhà nào không
            if ($chuNha->toaNhas()->exists()) {
                return response()->json(['message' => 'Không thể xóa chủ nhà này vì đang được sử dụng trong tòa nhà'], 400);
            }

            $chuNha->delete();
            return response()->json(['message' => 'Xóa chủ nhà thành công']);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa chủ nhà'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = ChuNha::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('cmnd', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            $chuNhas = $query->paginate(10);
            return ChuNhaResource::collection($chuNhas);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm chủ nhà: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tìm kiếm chủ nhà'], 500);
        }
    }
}
