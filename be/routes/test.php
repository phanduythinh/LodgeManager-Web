<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test route để kiểm tra kết nối
Route::get('/test-connection', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Kết nối thành công giữa frontend và backend!',
        'timestamp' => now()->toDateTimeString()
    ]);
});
