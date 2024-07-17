<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\SubscriptionForm;
use App\Livewire\AdhocPaymentForm;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/success', function () {
        dd( 'Payment successful!');
    })->name('payment.success');


Route::get('/subscribe', SubscriptionForm::class);
Route::get('/pay', AdhocPaymentForm::class);

Route::get('/session-test', function () {
    session(['test_key' => 'test_value']);
    return "Session set";
});

Route::get('/session-check', function () {
    return session('test_key', 'Session not set');
});


require __DIR__.'/auth.php';
