<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'room' => new RoomResource($this->whenLoaded('room')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'deposit_amount' => $this->deposit_amount,
            'monthly_rent' => $this->monthly_rent,
            'payment_day' => $this->payment_day,
            'description' => $this->description,
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
