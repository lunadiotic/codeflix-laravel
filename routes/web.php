<?php

use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subcription/plans', [SubscribeController::class, 'showPlans'])->name('subscription.plans');