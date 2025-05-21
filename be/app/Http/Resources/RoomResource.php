<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'area' => $this->area,
            'price' => $this->price,
            'status' => $this->status,
            'description' => $this->description,
            'building_id' => $this->building_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'building' => new BuildingResource($this->whenLoaded('building')),
            'contracts' => ContractResource::collection($this->whenLoaded('contracts')),
        ];
    }
}
