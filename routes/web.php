<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tutor/{id}', [\App\Http\Controllers\EducatorProfileController::class, 'show'])->name('tutor.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'educator') {
            return redirect()->route('dashboard.educator');
        }
        return redirect()->route('dashboard.parent');
    })->name('dashboard');
    
    Route::get('/dashboard/educator', [\App\Http\Controllers\EducatorDashboardController::class, 'index'])->name('dashboard.educator');
    Route::get('/dashboard/parent', [\App\Http\Controllers\ParentDashboardController::class, 'index'])->name('dashboard.parent');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
