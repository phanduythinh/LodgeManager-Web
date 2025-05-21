<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rental_price' => $this->rental_price,
            'deposit' => $this->deposit,
            'status' => $this->status,
            'room_id' => $this->room_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'room' => new RoomResource($this->whenLoaded('room')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
        ];
    }
}
