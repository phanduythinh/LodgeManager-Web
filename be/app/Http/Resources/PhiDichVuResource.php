<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhiDichVuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'toa_nha_id' => $this->toa_nha_id,
            'ma_dich_vu' => $this->ma_dich_vu,
            'ten_dich_vu' => $this->ten_dich_vu,
            'loai_dich_vu' => $this->loai_dich_vu,
            'don_gia' => $this->don_gia,
            'don_vi_tinh' => $this->don_vi_tinh,
            'toa_nha' => new ToaNhaResource($this->whenLoaded('toaNha')),
            'hop_dongs' => HopDongResource::collection($this->whenLoaded('hopDongs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
