<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiayToRequest;
use App\Http\Resources\GiayToResource;
use App\Models\GiayTo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GiayToController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $query = GiayTo::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('loai', 'like', "%{$keyword}%")
                        ->orWhere('noi_cap', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            $giayTos = $query->paginate(10);
            return GiayToResource::collection($giayTos);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách giấy tờ'
            ], 500);
        }
    }

    public function store(GiayToRequest $request): GiayToResource
    {
        try {
            $giayTo = GiayTo::create($request->validated());
            return new GiayToResource($giayTo);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo giấy tờ'
            ], 500);
        }
    }

    public function show($id): GiayToResource
    {
        try {
            $giayTo = GiayTo::findOrFail($id);
            return new GiayToResource($giayTo);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy giấy tờ'
            ], 404);
        }
    }

    public function update(GiayToRequest $request, $id): GiayToResource
    {
        try {
            $giayTo = GiayTo::findOrFail($id);
            $giayTo->update($request->validated());
            return new GiayToResource($giayTo);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật giấy tờ'
            ], 500);
        }
    }

    public function destroy($id): Response
    {
        try {
            $giayTo = GiayTo::findOrFail($id);
            $giayTo->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa giấy tờ'
            ], 500);
        }
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        try {
            $query = GiayTo::query();

            if ($request->has('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('ten', 'like', "%{$keyword}%")
                        ->orWhere('loai', 'like', "%{$keyword}%")
                        ->orWhere('noi_cap', 'like', "%{$keyword}%");
                });
            }

            if ($request->has('trang_thai')) {
                $query->where('trang_thai', $request->trang_thai);
            }

            if ($request->has('toa_nha_id')) {
                $query->where('toa_nha_id', $request->toa_nha_id);
            }

            $giayTos = $query->paginate(10);
            return GiayToResource::collection($giayTos);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm kiếm giấy tờ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tìm kiếm giấy tờ'
            ], 500);
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
            $path = $file->storeAs('giay-to', $fileName, 'public');

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
            $giayTo = GiayTo::findOrFail($id);

            if (!$giayTo->file_path) {
                return response()->json(['message' => 'Không tìm thấy file'], 404);
            }

            if (!Storage::disk('public')->exists($giayTo->file_path)) {
                return response()->json(['message' => 'File không tồn tại'], 404);
            }

            return Storage::disk('public')->download($giayTo->file_path);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải file: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tải file'], 500);
        }
    }
}
