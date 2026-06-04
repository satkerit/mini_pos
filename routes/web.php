<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Livewire\Pos;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect('/admin');
        }
        return redirect('/pos');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect('/admin');
    }
    return redirect('/pos');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/pos', Pos::class)->middleware(['auth', 'role:admin|cashier'])->name('pos');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
