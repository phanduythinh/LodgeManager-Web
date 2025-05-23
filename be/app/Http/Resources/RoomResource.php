<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'building' => new BuildingResource($this->whenLoaded('building')),
            'status' => $this->status,
            'price' => $this->price,
            'area' => $this->area,
            'floor' => $this->floor,
            'description' => $this->description,
            'contracts' => ContractResource::collection($this->whenLoaded('contracts')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
