<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaoCaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ten' => $this->ten,
            'loai' => $this->loai,
            'ngay_tao' => $this->ngay_tao,
            'ngay_cap_nhat' => $this->ngay_cap_nhat,
            'noi_dung' => $this->noi_dung,
            'file_path' => $this->file_path,
            'toa_nha' => new ToaNhaResource($this->whenLoaded('toaNha')),
            'nguoi_tao' => new UserResource($this->whenLoaded('nguoiTao')),
            'trang_thai' => $this->trang_thai,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
