<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnakController;
use App\Http\Controllers\Api\PengukuranController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('/anak', AnakController::class)->names([
        'index' => 'api.anak.index',
        'store' => 'api.anak.store',
        'show' => 'api.anak.show',
        'update' => 'api.anak.update',
        'destroy' => 'api.anak.destroy',
    ]);
    Route::post('/anak/{id}/pengukuran', [PengukuranController::class, 'store'])->name('api.pengukuran.store');
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
