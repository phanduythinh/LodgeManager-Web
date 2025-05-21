<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'document_number' => $this->document_number,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'status' => $this->status,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'building' => new BuildingResource($this->whenLoaded('building')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
