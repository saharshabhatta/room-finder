<?php

use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dummyAPI;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("data",[dummyAPI::class,'getData']);

Route::get("list/{id?}", [dummyAPI::class, 'list']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get("user", [dummyAPI::class, 'getUser']);
    Route::post("add-user", [dummyAPI::class, 'createUser']);
    Route::put("update-user", [dummyAPI::class, 'updateUser']);
    Route::delete("delete-user/{id}", [dummyAPI::class, 'deleteUser']);
});

Route::get("search-user/{name}",[dummyAPI::class, 'searchUser']);

Route::middleware('web')->group(function () {
    Route::post('/login', [dummyAPI::class, 'loginUser']);
});

Route::post("signupUser", [dummyAPI::class, 'signupUser']);

Route::fallback(function () {
    return response()->json(['error' => 'Not Found'], 404);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('room_types', RoomTypeController::class);
    Route::apiResource('images', ImageController::class);
});
