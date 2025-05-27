<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToaNhaRequest;
use App\Models\ToaNha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Buildings",
 *     description="API Endpoints for managing buildings"
 * )
 */
class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/buildings",
     *     summary="Get list of buildings",
     *     tags={"Buildings"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for building name or address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by building status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive", "maintenance"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of buildings",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Building")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/buildings",
     *     summary="Create a new building",
     *     tags={"Buildings"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BuildingRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Building created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/buildings/{id}",
     *     summary="Get building details",
     *     tags={"Buildings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building details",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/buildings/{id}",
     *     summary="Update building details",
     *     tags={"Buildings"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BuildingRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/buildings/{id}",
     *     summary="Delete a building",
     *     tags={"Buildings"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Building deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found"
     *     )
     * )
     */
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
