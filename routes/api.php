<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ManagerController::class, 'store']);
Route::post('/login', [ManagerController::class, 'login']);
Route::post('/createuser',[UserController::class, 'createUser'])->middleware('auth:sanctum');
Route::post('/addbalance/{userID}',[BalanceController::class, 'store'])->middleware('auth:sanctum');
Route::post('/expense',[ExpenseController::class, 'store'])->middleware('auth:sanctum');
Route::post('/userlogin',[UserController::class, 'loginUser']);
