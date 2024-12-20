<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subcription/plans', function () {
    return 'Subscription plans';
})->name('subscription.plans');