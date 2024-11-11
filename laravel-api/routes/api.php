<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('list',[PostController::class,'index']);
    Route::post('store',[PostController::class,'store']);
    Route::get('edit/{id}',[PostController::class,'edit']);
    Route::post('update/{id}',[PostController::class,'update']);
    Route::post('delete/{id}',[PostController::class,'delete']);
    Route::get('logout',[AuthController::class,'logout']);
});


