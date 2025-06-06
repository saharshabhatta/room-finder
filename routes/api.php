<?php

use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dummyAPI;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::get("data",[dummyAPI::class,'getData']);
//
//Route::get("list/{id?}", [dummyAPI::class, 'list']);
//
//Route::group(['middleware' => 'auth:sanctum'], function () {
//    Route::get("user", [dummyAPI::class, 'getUser']);
//    Route::post("add-user", [dummyAPI::class, 'createUser']);
//    Route::put("update-user", [dummyAPI::class, 'updateUser']);
//    Route::delete("delete-user/{id}", [dummyAPI::class, 'deleteUser']);
//});

//Route::get("search-user/{name}",[dummyAPI::class, 'searchUser']);

Route::middleware('web')->group(function () {
    Route::post('/login', [dummyAPI::class, 'loginUser']);
});

Route::post("signupUser", [dummyAPI::class, 'signupUser']);

Route::fallback(function () {
    return response()->json(['error' => 'Not Found'], 404);
});

Route::get('rooms', [RoomController::class, 'index']);
Route::middleware('auth:sanctum')->apiResource('rooms', RoomController::class)->except(['index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('rooms/search', [RoomController::class, 'search']);
    Route::post('rooms/filter', [RoomController::class, 'filter']);
    Route::get('/rooms/user/{user}', [RoomController::class, 'getUserRooms']);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('room_types', RoomTypeController::class);
    Route::apiResource('images', ImageController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/favourites/toggle', [FavouriteController::class, 'toggleFavourite']);
    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourite.index');
    Route::post('/interests/toggle', [InterestController::class, 'toggleInterest']);
    Route::get('/interests', [InterestController::class, 'getInterests']);
    Route::get('interests/my-rooms', [InterestController::class,'getInterestsByRooms']);
    Route::get('/interests/{id}', [InterestController::class, 'getInterestById']);
});

Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');

//Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])
//        ->name('profile.edit');
//
//    Route::put('/profile', [ProfileController::class, 'update'])
//        ->name('profile.update');
//
//    Route::delete('/profile', [ProfileController::class, 'destroy'])
//        ->name('profile.destroy');
//});
