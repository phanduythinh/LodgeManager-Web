<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhongResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'toa_nha_id' => $this->toa_nha_id,
            'ma_phong' => $this->ma_phong,
            'ten_phong' => $this->ten_phong,
            'tang' => $this->tang,
            'gia_thue' => $this->gia_thue,
            'dat_coc' => $this->dat_coc,
            'dien_tich' => $this->dien_tich,
            'so_khach_toi_da' => $this->so_khach_toi_da,
            'trang_thai' => $this->trang_thai,
            'toa_nha' => new ToaNhaResource($this->whenLoaded('toaNha')),
            'hop_dongs' => HopDongResource::collection($this->whenLoaded('hopDongs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
