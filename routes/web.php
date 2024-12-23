<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [MovieController::class, 'index'])->middleware(['auth', 'check.device'])->name('home');

Route::group(['middleware' => ['auth', 'check.device']], function () {
    Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
    Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');
});

Route::get('/subcription/plans', [SubscribeController::class, 'showPlans'])->name('subscription.plans');
Route::get('/subcription/subscribe/success', [SubscribeController::class, 'subscribeSuccess'])->name('subscription.success');
Route::get('/subcription/subscribe/{plan}', [SubscribeController::class, 'checkoutPlan'])->name('subscription.checkout');
Route::post('/subcription/subscribe', [SubscribeController::class, 'subscribe'])->name('subscription.subscribe');