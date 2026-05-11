<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\SmokingController;
use App\Http\Controllers\MonthlyController;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('medicine.index'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected
Route::middleware('auth')->group(function () {

    // ঔষধ — default landing page
    Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::post('/medicine/{id}/toggle', [MedicineController::class, 'toggle'])->name('medicine.toggle');

    // নামাজ
    Route::get('/prayer', [PrayerController::class, 'index'])->name('prayer.index');
    Route::post('/prayer/{id}/toggle', [PrayerController::class, 'toggle'])->name('prayer.toggle');

    // ব্যায়াম
    Route::get('/exercise', [ExerciseController::class, 'index'])->name('exercise.index');
    Route::post('/exercise/{id}/toggle', [ExerciseController::class, 'toggle'])->name('exercise.toggle');

    // ধূমপান
    Route::get('/smoking', [SmokingController::class, 'index'])->name('smoking.index');
    Route::post('/smoking/{id}/toggle', [SmokingController::class, 'toggle'])->name('smoking.toggle');

    // মাসিক
    Route::get('/monthly', [MonthlyController::class, 'index'])->name('monthly.index');
    Route::get('/monthly/stats', [MonthlyController::class, 'stats'])->name('monthly.stats');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});