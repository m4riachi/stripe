<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/create-customer', [CustomerController::class, 'createCustomer']);
Route::get('/charge-customer', [CustomerController::class, 'chargeCustomer']);
