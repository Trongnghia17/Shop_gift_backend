<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\FrontendController;

//dang ky
Route::post('register', [AuthController::class, 'register']);
// dang nhap
Route::post('login', [AuthController::class, 'login']);
Route::post('get-qr-code', [AuthController::class, 'getQrCode']);

Route::controller(FrontendController::class)->group(function () {
    Route::get('viewHomePage', 'index');
    Route::get('getCategory', 'category');
    Route::get('fetchproducts/{slug}', 'product');
    Route::get('viewproductdetail/{category_slug}/{product_slug}', 'viewproduct');
});

Route::middleware('auth:sanctum', 'isAPIAdmin')->group(function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'Bạn đã đăng nhập', 'status' => Response::HTTP_OK], Response::HTTP_OK);
    });
    // Category
    Route::controller(CategoryController::class)->group(function () {
        Route::get('view-category', 'index');
        Route::post('store-category', 'store');
        Route::get('edit-category/{id}', 'edit');
        Route::put('update-category/{id}', 'update');
        Route::delete('delete-category/{id}', 'destroy');
        Route::get('all-category', 'allcategory');
    });
    // Product
    Route::controller(ProductController::class)->group(function () {
        Route::post('store-product', 'store');
        Route::get('view-product', 'index');
        Route::get('edit-product/{id}', 'edit');
        Route::put('update-product/{id}', 'update');
        Route::delete('delete-product/{id}', 'destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
