<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ToaNhaResource;
use App\Models\ToaNha;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class ToaNhaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $toaNhas = ToaNha::with(['phongs', 'phiDichVus'])->get();
        return ToaNhaResource::collection($toaNhas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ma_nha' => 'required|unique:toa_nhas',
            'ten_nha' => 'required',
            'dia_chi_nha' => 'required',
            'xa_phuong' => 'required',
            'quan_huyen' => 'required',
            'tinh_thanh' => 'required',
            'trang_thai' => 'required'
        ]);

        $toaNha = ToaNha::create($validated);
        return (new ToaNhaResource($toaNha))->response()->setStatusCode(201);
    }

    public function show(ToaNha $toaNha): ToaNhaResource
    {
        $toaNha->load(['phongs', 'phiDichVus']);
        return new ToaNhaResource($toaNha);
    }

    public function update(Request $request, ToaNha $toaNha): ToaNhaResource
    {
        $validated = $request->validate([
            'ten_nha' => 'required',
            'dia_chi_nha' => 'required',
            'xa_phuong' => 'required',
            'quan_huyen' => 'required',
            'tinh_thanh' => 'required',
            'trang_thai' => 'required'
        ]);

        $toaNha->update($validated);
        return new ToaNhaResource($toaNha);
    }

    public function destroy(ToaNha $toaNha): JsonResponse
    {
        $toaNha->delete();
        return response()->json(null, 204);
    }
}
