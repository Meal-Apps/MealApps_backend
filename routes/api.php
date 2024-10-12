<?php

use App\Http\Controllers\ManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ManagerController::class, 'store']);
Route::post('/login', [ManagerController::class, 'login']);
Route::post('/createuser',[UserController::class, 'createUser'])->middleware('auth:sanctum');
Route::post('/userlogin',[UserController::class, 'loginUser']);
