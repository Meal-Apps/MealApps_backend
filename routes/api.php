<?php

use App\Http\Controllers\ManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [ManagerController::class, 'store']);
Route::post('/login', [ManagerController::class, 'login']);
