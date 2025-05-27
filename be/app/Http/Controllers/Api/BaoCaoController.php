<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaoCaoRequest;
use App\Http\Resources\BaoCaoResource;
use App\Models\BaoCao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BaoCaoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = BaoCao::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('loai', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            if ($request->has('nguoi_tao_id')) {
                $query->where('nguoi_tao_id', $request->nguoi_tao_id);
            }

            $baoCaos = $query->paginate(10);
            return BaoCaoResource::collection($baoCaos);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách báo cáo'], 500);
        }
    }

    public function store(BaoCaoRequest $request)
    {
        try {
            $baoCao = BaoCao::create($request->validated());
            return new BaoCaoResource($baoCao);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tạo báo cáo'], 500);
        }
    }

    public function show($id)
    {
        try {
            $baoCao = BaoCao::findOrFail($id);
            return new BaoCaoResource($baoCao);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Không tìm thấy báo cáo'], 404);
        }
    }

    public function update(BaoCaoRequest $request, $id)
    {
        try {
            $baoCao = BaoCao::findOrFail($id);
            $baoCao->update($request->validated());
            return new BaoCaoResource($baoCao);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật báo cáo'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $baoCao = BaoCao::findOrFail($id);
            $baoCao->delete();
            return response()->json(['message' => 'Xóa báo cáo thành công']);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa báo cáo'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = BaoCao::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('loai', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            if ($request->has('nguoi_tao_id')) {
                $query->where('nguoi_tao_id', $request->nguoi_tao_id);
            }

            $baoCaos = $query->paginate(10);
            return BaoCaoResource::collection($baoCaos);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm báo cáo: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tìm kiếm báo cáo'], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['message' => 'Không tìm thấy file'], 400);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bao-cao', $fileName, 'public');

            return response()->json([
                'message' => 'Upload file thành công',
                'file_path' => $path
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi upload file: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi upload file'], 500);
        }
    }

    public function downloadFile($id)
    {
        try {
            $baoCao = BaoCao::findOrFail($id);

            if (!$baoCao->file_path) {
                return response()->json(['message' => 'Không tìm thấy file'], 404);
            }

            if (!Storage::disk('public')->exists($baoCao->file_path)) {
                return response()->json(['message' => 'File không tồn tại'], 404);
            }

            return Storage::disk('public')->download($baoCao->file_path);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải file: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tải file'], 500);
        }
    }
}
