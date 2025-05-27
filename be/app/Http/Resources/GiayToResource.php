<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiayToResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ten' => $this->ten,
            'loai' => $this->loai,
            'ngay_cap' => $this->ngay_cap,
            'noi_cap' => $this->noi_cap,
            'ngay_het_han' => $this->ngay_het_han,
            'file_path' => $this->file_path,
            'toa_nha' => new ToaNhaResource($this->whenLoaded('toaNha')),
            'ghi_chu' => $this->ghi_chu,
            'trang_thai' => $this->trang_thai,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
