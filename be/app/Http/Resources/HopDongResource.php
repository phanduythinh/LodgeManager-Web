<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HopDongResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ma_hop_dong' => $this->ma_hop_dong,
            'phong_id' => $this->phong_id,
            'ngay_bat_dau' => $this->ngay_bat_dau,
            'ngay_ket_thuc' => $this->ngay_ket_thuc,
            'tien_thue' => $this->tien_thue,
            'tien_coc' => $this->tien_coc,
            'chu_ky_thanh_toan' => $this->chu_ky_thanh_toan,
            'ngay_tinh_tien' => $this->ngay_tinh_tien,
            'trang_thai' => $this->trang_thai,
            'phong' => new PhongResource($this->whenLoaded('phong')),
            'khach_hangs' => KhachHangResource::collection($this->whenLoaded('khachHangs')),
            'phi_dich_vus' => PhiDichVuResource::collection($this->whenLoaded('phiDichVus')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
