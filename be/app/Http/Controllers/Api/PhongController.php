<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhongResource;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class PhongController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $phongs = Phong::with(['toaNha'])->get();
        return PhongResource::collection($phongs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'toa_nha_id' => 'required|exists:toa_nhas,id',
            'ma_phong' => 'required|unique:phongs',
            'ten_phong' => 'required',
            'tang' => 'required',
            'gia_thue' => 'required|numeric',
            'dat_coc' => 'required|numeric',
            'dien_tich' => 'required|numeric',
            'so_khach_toi_da' => 'required|integer',
            'trang_thai' => 'required'
        ]);

        $phong = Phong::create($validated);
        return (new PhongResource($phong))->response()->setStatusCode(201);
    }

    public function show(Phong $phong): PhongResource
    {
        $phong->load(['toaNha']);
        return new PhongResource($phong);
    }

    public function update(Request $request, Phong $phong): PhongResource
    {
        $validated = $request->validate([
            'ten_phong' => 'required',
            'tang' => 'required',
            'gia_thue' => 'required|numeric',
            'dat_coc' => 'required|numeric',
            'dien_tich' => 'required|numeric',
            'so_khach_toi_da' => 'required|integer',
            'trang_thai' => 'required'
        ]);

        $phong->update($validated);
        return new PhongResource($phong);
    }

    public function destroy(Phong $phong): JsonResponse
    {
        $phong->delete();
        return response()->json(null, 204);
    }
}
