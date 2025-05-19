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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes
Route::apiResource('owners', OwnerController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('buildings', BuildingController::class);
Route::apiResource('rooms', RoomController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('contracts', ContractController::class);
Route::apiResource('legal-documents', LegalDocumentController::class);
Route::apiResource('reports', ReportController::class);
