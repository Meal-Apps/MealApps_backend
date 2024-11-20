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

Route::post('/forgot-password', [ManagerController::class, 'forgotPassword']);
Route::post('/reset-password', [ManagerController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/getallusers', [UserController::class, 'getAllUsers']);
    Route::get('/getallbalance/{month}', [BalanceController::class, 'getBalanceByMonth']);
    Route::get('/getallexpenses/{month}', [ExpenseController::class, 'getExpensesByMonth']);
    Route::post('/logout', [UserController::class, 'logoutUser']);
    Route::get('/getManagers', [ManagerController::class, 'index']);


});
Route::middleware(['auth:sanctum', 'manager'])->group(function () {

    Route::post('/logoutmanager', [ManagerController::class, 'logoutManager']);
    Route::post('/createuser', [UserController::class, 'createUser']);
    Route::post('/addbalance', [BalanceController::class, 'store']);
    Route::post('/addexpense', [ExpenseController::class, 'store']);
    Route::delete('/deleteexpense/{id}', [ExpenseController::class, 'destroy']);
    Route::delete('/deletemanager/{id}', [BalanceController::class, 'destroy']);
    Route::get('/usersearch', [UserController::class, 'userSearch']);
    Route::get('/usersearch/{query}', [UserController::class, 'userSearchapp']);
    Route::delete('/deleteuser/{id}', [UserController::class, 'deleteUser']);

});
