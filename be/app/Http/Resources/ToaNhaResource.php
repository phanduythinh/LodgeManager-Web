<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToaNhaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_nha' => $this->ma_nha,
            'ten_nha' => $this->ten_nha,
            'dia_chi_nha' => $this->dia_chi_nha,
            'xa_phuong' => $this->xa_phuong,
            'quan_huyen' => $this->quan_huyen,
            'tinh_thanh' => $this->tinh_thanh,
            'trang_thai' => $this->trang_thai,
            'phongs' => PhongResource::collection($this->whenLoaded('phongs')),
            'phi_dich_vus' => PhiDichVuResource::collection($this->whenLoaded('phiDichVus')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
