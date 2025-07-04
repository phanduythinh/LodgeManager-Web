<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ToaNhaController;
use App\Http\Controllers\Api\PhongController;
use App\Http\Controllers\Api\KhachHangController;
use App\Http\Controllers\Api\HopDongController;
use App\Http\Controllers\Api\PhiDichVuController;
use App\Http\Controllers\Api\HoaDonController;
use App\Http\Controllers\Api\GiayToController;
use App\Http\Controllers\Api\BaoCaoController;
use App\Http\Controllers\Api\ChuNhaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// ToaNha routes - public for testing
Route::apiResource('toa-nha', ToaNhaController::class);
Route::get('toa-nha/{id}/phong', [ToaNhaController::class, 'getPhongs']);
Route::get('toa-nha/search', [ToaNhaController::class, 'search']);

// Phong routes - public for testing
Route::apiResource('phong', PhongController::class);
Route::get('phong/{id}/hop-dong', [PhongController::class, 'getHopDong']);
Route::get('phong/{id}/hoa-don', [PhongController::class, 'getHoaDon']);
Route::get('phong/search', [PhongController::class, 'search']);

// PhiDichVu routes - public for testing
Route::apiResource('phi-dich-vu', PhiDichVuController::class);
Route::get('phi-dich-vu/search', [PhiDichVuController::class, 'search']);

// KhachHang routes - public for testing
Route::apiResource('khach-hang', KhachHangController::class);
Route::get('khach-hang/search', [KhachHangController::class, 'search']);
Route::get('khach-hang/{id}/hop-dong', [KhachHangController::class, 'getHopDongs']);

// HopDong routes - public for testing
Route::apiResource('hop-dong', HopDongController::class);
Route::get('hop-dong/{id}/hoa-don', [HopDongController::class, 'getHoaDons']);
Route::get('hop-dong/search', [HopDongController::class, 'search']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // All routes moved to public for testing

    // HoaDon routes
    Route::apiResource('hoa-don', HoaDonController::class);
    Route::post('hoa-don/{id}/thanh-toan', [HoaDonController::class, 'thanhToan']);
    Route::get('hoa-don/search', [HoaDonController::class, 'search']);

    // GiayTo routes
    Route::get('giay-to/search', [GiayToController::class, 'search']);
    Route::post('giay-to/upload', [GiayToController::class, 'uploadFile']);
    Route::get('giay-to/{id}/download', [GiayToController::class, 'downloadFile']);
    Route::apiResource('giay-to', GiayToController::class);

    // BaoCao routes
    Route::get('bao-cao/search', [BaoCaoController::class, 'search']);
    Route::post('bao-cao/upload', [BaoCaoController::class, 'uploadFile']);
    Route::get('bao-cao/{id}/download', [BaoCaoController::class, 'downloadFile']);
    Route::apiResource('bao-cao', BaoCaoController::class);

    // ChuNha routes
    Route::get('chu-nha/search', [ChuNhaController::class, 'search']);
    Route::apiResource('chu-nha', ChuNhaController::class);
});

// Test routes
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/ping', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Kết nối thành công!',
        'timestamp' => now()->toDateTimeString()
    ]);
});
