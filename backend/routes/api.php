<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController; 

use App\Http\Controllers\Api\KategoriController; 

use App\Http\Controllers\API\PengembalianController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']); 

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/me', [AuthController::class, 'me']); 
    Route::post('/logout', [AuthController::class, 'logoutApi']); 

    Route::middleware('role.admin')->group(function () {
        Route::apiResource('kategori', KategoriController::class);

        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);
    });

    Route::middleware('role.petugas')->group(function () {
        Route::post('/pengembalian', [PengembalianController::class, 'store']);
    });

    Route::middleware('role.peminjam')->group(function () {

    });
});