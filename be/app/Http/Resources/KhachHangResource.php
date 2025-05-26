<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KhachHangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_khach_hang' => $this->ma_khach_hang,
            'ho_ten' => $this->ho_ten,
            'so_dien_thoai' => $this->so_dien_thoai,
            'email' => $this->email,
            'cccd' => $this->cccd,
            'gioi_tinh' => $this->gioi_tinh,
            'ngay_sinh' => $this->ngay_sinh,
            'dia_chi_nha' => $this->dia_chi_nha,
            'xa_phuong' => $this->xa_phuong,
            'quan_huyen' => $this->quan_huyen,
            'tinh_thanh' => $this->tinh_thanh,
            'hop_dongs' => HopDongResource::collection($this->whenLoaded('hopDongs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 