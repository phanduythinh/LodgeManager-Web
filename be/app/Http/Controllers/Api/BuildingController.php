<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $buildings = Building::with(['owner', 'rooms'])->paginate(10);
        return BuildingResource::collection($buildings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuildingRequest $request): BuildingResource
    {
        $building = Building::create($request->validated());
        return new BuildingResource($building);
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building): BuildingResource
    {
        $building->load(['owner', 'rooms']);
        return new BuildingResource($building);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuildingRequest $request, Building $building): BuildingResource
    {
        $building->update($request->validated());
        return new BuildingResource($building);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building): Response
    {
        $building->delete();
        return response()->noContent();
    }
}
