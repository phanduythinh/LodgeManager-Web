<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'total_floors' => $this->total_floors,
            'total_rooms' => $this->total_rooms,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'owner' => new OwnerResource($this->whenLoaded('owner')),
        ];
    }
}
