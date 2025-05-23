<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'address' => $this->address,
            'id_card' => $this->id_card,
            'id_card_issue_date' => $this->id_card_issue_date,
            'id_card_issue_place' => $this->id_card_issue_place,
            'note' => $this->note,
            'contracts' => ContractResource::collection($this->whenLoaded('contracts')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
