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
    public function index(Request $request): JsonResponse
    {
        $query = Building::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $buildings = $query->paginate(10);

        return response()->json($buildings);
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
    public function store(BuildingRequest $request): JsonResponse
    {
        $building = Building::create($request->validated());

        return response()->json($building, 201);
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
    public function show(Building $building): JsonResponse
    {
        return response()->json($building);
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
    public function update(BuildingRequest $request, Building $building): JsonResponse
    {
        $building->update($request->validated());

        return response()->json($building);
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
    public function destroy(Building $building): JsonResponse
    {
        $building->delete();

        return response()->json(null, 204);
    }
}
