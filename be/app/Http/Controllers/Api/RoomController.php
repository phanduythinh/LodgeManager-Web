<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $rooms = Room::with(['building', 'contracts'])->paginate(10);
        return RoomResource::collection($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request): RoomResource
    {
        $room = Room::create($request->validated());
        return new RoomResource($room);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): RoomResource
    {
        $room->load(['building', 'contracts']);
        return new RoomResource($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, Room $room): RoomResource
    {
        $room->update($request->validated());
        return new RoomResource($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): Response
    {
        $room->delete();
        return response()->noContent();
    }
}
