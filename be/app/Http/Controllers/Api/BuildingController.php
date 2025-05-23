<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

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
    public function index(): JsonResponse
    {
        // Return dummy data for testing
        return response()->json([
            'data' => [
                [
                    'id' => 'TN-001',
                    'TenNha' => 'Tòa nhà Test 1',
                    'DiaChiNha' => '123 Test Street',
                    'TinhThanh' => 'Hà Nội',
                    'QuanHuyen' => 'Cầu Giấy',
                    'XaPhuong' => 'Dịch Vọng',
                    'TrangThai' => 'Hoạt động'
                ],
                [
                    'id' => 'TN-002',
                    'TenNha' => 'Tòa nhà Test 2',
                    'DiaChiNha' => '456 Test Avenue',
                    'TinhThanh' => 'Hà Nội',
                    'QuanHuyen' => 'Ba Đình',
                    'XaPhuong' => 'Kim Mã',
                    'TrangThai' => 'Hoạt động'
                ]
            ]
        ]);
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
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Building created successfully',
            'data' => $request->all()
        ], 201);
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
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $id,
                'TenNha' => 'Tòa nhà Test ' . $id,
                'DiaChiNha' => '123 Test Street',
                'TinhThanh' => 'Hà Nội',
                'QuanHuyen' => 'Cầu Giấy',
                'XaPhuong' => 'Dịch Vọng',
                'TrangThai' => 'Hoạt động'
            ]
        ]);
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
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Building updated successfully',
            'data' => array_merge(['id' => $id], $request->all())
        ]);
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
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Building deleted successfully',
            'id' => $id
        ]);
    }
}
