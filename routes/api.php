<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

//dang ky
Route::post('register', [AuthController::class, 'register']);
// dang nhap
Route::post('login', [AuthController::class, 'login']);
Route::post('get-qr-code', [AuthController::class, 'getQrCode']);

Route::middleware('auth:sanctum', 'isAPIAdmin')->group(function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'Bạn đã đăng nhập', 'status' => Response::HTTP_OK], Response::HTTP_OK);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
