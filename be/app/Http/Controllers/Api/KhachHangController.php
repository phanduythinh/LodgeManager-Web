<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KhachHangResource;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class KhachHangController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $khachHangs = KhachHang::with(['hopDongs'])->get();
        return KhachHangResource::collection($khachHangs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ma_khach_hang' => 'required|unique:khach_hangs',
            'ho_ten' => 'required',
            'so_dien_thoai' => 'required',
            'email' => 'required|email|unique:khach_hangs',
            'cccd' => 'required|unique:khach_hangs',
            'gioi_tinh' => 'required',
            'ngay_sinh' => 'required|date',
            'dia_chi_nha' => 'required',
            'xa_phuong' => 'required',
            'quan_huyen' => 'required',
            'tinh_thanh' => 'required'
        ]);

        $khachHang = KhachHang::create($validated);
        return (new KhachHangResource($khachHang))->response()->setStatusCode(201);
    }

    public function show(KhachHang $khachHang): KhachHangResource
    {
        $khachHang->load(['hopDongs']);
        return new KhachHangResource($khachHang);
    }

    public function update(Request $request, KhachHang $khachHang): KhachHangResource
    {
        $validated = $request->validate([
            'ho_ten' => 'required',
            'so_dien_thoai' => 'required',
            'email' => 'required|email|unique:khach_hangs,email,' . $khachHang->id,
            'cccd' => 'required|unique:khach_hangs,cccd,' . $khachHang->id,
            'gioi_tinh' => 'required',
            'ngay_sinh' => 'required|date',
            'dia_chi_nha' => 'required',
            'xa_phuong' => 'required',
            'quan_huyen' => 'required',
            'tinh_thanh' => 'required'
        ]);

        $khachHang->update($validated);
        return new KhachHangResource($khachHang);
    }

    public function destroy(KhachHang $khachHang): JsonResponse
    {
        $khachHang->delete();
        return response()->json(null, 204);
    }
}
