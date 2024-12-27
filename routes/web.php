<?php

use App\Events\MembershipHasExpired;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\TransactionController;
use App\Models\Membership;
use Illuminate\Support\Facades\Route;


Route::get('/home', [MovieController::class, 'index'])->middleware(['auth', 'check.device'])->name('home');

Route::group(['middleware' => ['auth', 'check.device']], function () {
    Route::get('/', [MovieController::class, 'index'])->name('welcome');
    Route::get('/movies', [MovieController::class, 'all'])->name('movies.index');
    Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');
    Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
    Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');

    Route::get('/profile/overview', [ProfileController::class, 'overview'])->name('profile.overview');
});

Route::get('/subcription/plans', [SubscribeController::class, 'showPlans'])->name('subscription.plans');
Route::get('/subcription/subscribe/success', [SubscribeController::class, 'subscribeSuccess'])->name('subscription.success');
Route::get('/subcription/subscribe/{plan}', [SubscribeController::class, 'checkoutPlan'])->name('subscription.checkout');
Route::post('/subcription/subscribe', [SubscribeController::class, 'subscribe'])->name('subscription.subscribe');

Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

Route::get('/test-expired', function () {
    $membership = Membership::find(1);

    event(new MembershipHasExpired($membership));

    return "Event fired, check MailHog";
});
