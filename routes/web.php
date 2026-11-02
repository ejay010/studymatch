<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EducatorProfileController;
use App\Http\Controllers\EducatorDashboardController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\DigitalResourceDownloadController;
use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tutor/{id}', [EducatorProfileController::class, 'show'])->name('tutor.show');

Route::view('/classes', 'pages.classes')->name('classes.index');
Route::view('/resources', 'pages.resources')->name('resources.index');
Route::get('/resources/{resource}/download', [DigitalResourceDownloadController::class, 'download'])->name('resources.download');
Route::view('/apply', 'pages.apply')->name('apply.index');

// PayPal Payment Callbacks
Route::get('/payment/paypal/return', [PaymentCallbackController::class, 'handleReturn'])->name('payment.paypal.return');
Route::get('/payment/paypal/cancel', [PaymentCallbackController::class, 'handleCancel'])->name('payment.paypal.cancel');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'educator') {
            return redirect()->route('dashboard.educator');
        }
        return redirect()->route('dashboard.parent');
    })->name('dashboard');
    
    Route::get('/dashboard/educator', [EducatorDashboardController::class, 'index'])->name('dashboard.educator');
    Route::get('/dashboard/parent', [ParentDashboardController::class, 'index'])->name('dashboard.parent');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
