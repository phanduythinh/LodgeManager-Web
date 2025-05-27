<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChuNhaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ho_ten' => $this->ho_ten,
            'so_dien_thoai' => $this->so_dien_thoai,
            'email' => $this->email,
            'dia_chi' => $this->dia_chi,
            'cmnd' => $this->cmnd,
            'ngay_cap_cmnd' => $this->ngay_cap_cmnd,
            'noi_cap_cmnd' => $this->noi_cap_cmnd,
            'ngay_sinh' => $this->ngay_sinh,
            'gioi_tinh' => $this->gioi_tinh,
            'trang_thai' => $this->trang_thai,
            'toa_nhas' => ToaNhaResource::collection($this->whenLoaded('toaNhas')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
