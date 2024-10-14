<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ManagerController::class, 'store']);
Route::post('/login', [ManagerController::class, 'login']);

Route::post('/userlogin',[UserController::class, 'loginUser']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/getallusers', [UserController::class, 'getAllUsers']);
    Route::get('/getallbalance', [BalanceController::class, 'getAllBalances']);
});
Route::middleware(['auth:sanctum', 'manager'])->group(function () {
    Route::post('/createuser', [UserController::class, 'createUser']);
    Route::post('/addbalance/{userID}', [BalanceController::class, 'store']);
    Route::post('/expense', [ExpenseController::class, 'store']);

});
