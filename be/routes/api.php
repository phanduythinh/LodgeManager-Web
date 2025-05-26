<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\LegalDocumentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ToaNhaController;
use App\Http\Controllers\Api\PhongController;
use App\Http\Controllers\Api\KhachHangController;
use App\Http\Controllers\Api\HopDongController;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route::apiResource('buildings', BuildingController::class); // Commented out for testing
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('contracts', ContractController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('owners', OwnerController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('legal-documents', LegalDocumentController::class);
    Route::apiResource('reports', ReportController::class);
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Building routes - moved outside auth middleware for testing
Route::apiResource('buildings', BuildingController::class);

// ToaNha routes
Route::apiResource('toa-nhas', ToaNhaController::class);

// Phong routes
Route::apiResource('phongs', PhongController::class);

// KhachHang routes
Route::apiResource('khach-hangs', KhachHangController::class);

// HopDong routes
Route::apiResource('hop-dongs', HopDongController::class);
