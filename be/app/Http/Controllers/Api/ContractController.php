<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Contracts",
 *     description="API Endpoints for managing contracts"
 * )
 */
class ContractController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contracts",
     *     summary="Get list of contracts",
     *     tags={"Contracts"},
     *     @OA\Parameter(
     *         name="room_id",
     *         in="query",
     *         description="Filter by room ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         description="Filter by customer ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by contract status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "expired", "terminated"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of contracts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Contract")
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
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Contract::query();

        if ($request->has('room_id')) {
            $query->where('room_id', $request->get('room_id'));
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $contracts = $query->with(['room', 'customer', 'services'])->paginate(10);

        return ContractResource::collection($contracts);
    }

    /**
     * @OA\Post(
     *     path="/api/contracts",
     *     summary="Create a new contract",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ContractRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contract created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
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
    public function store(ContractRequest $request): ContractResource
    {
        $contract = Contract::create($request->validated());

        if ($request->has('service_ids')) {
            $contract->services()->attach($request->get('service_ids'));
        }

        return new ContractResource($contract);
    }

    /**
     * @OA\Get(
     *     path="/api/contracts/{id}",
     *     summary="Get contract details",
     *     tags={"Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contract details",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contract not found"
     *     )
     * )
     */
    public function show(Contract $contract): ContractResource
    {
        $contract->load(['room', 'customer', 'services']);
        return new ContractResource($contract);
    }

    /**
     * @OA\Put(
     *     path="/api/contracts/{id}",
     *     summary="Update contract details",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ContractRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contract updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contract")
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
     *         description="Contract not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(ContractRequest $request, Contract $contract): ContractResource
    {
        $contract->update($request->validated());

        if ($request->has('service_ids')) {
            $contract->services()->sync($request->get('service_ids'));
        }

        return new ContractResource($contract);
    }

    /**
     * @OA\Delete(
     *     path="/api/contracts/{id}",
     *     summary="Delete a contract",
     *     tags={"Contracts"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Contract ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Contract deleted successfully"
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
     *         description="Contract not found"
     *     )
     * )
     */
    public function destroy(Contract $contract): JsonResponse
    {
        $contract->delete();

        return response()->json(null, 204);
    }
}
