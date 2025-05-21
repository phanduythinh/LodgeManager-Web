<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'contract_id' => $this->contract_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
