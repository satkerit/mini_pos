<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Livewire\Pos;
use App\Livewire\ShiftPage;
use App\Livewire\HistoryPage;

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
Route::get('/pos/shift', ShiftPage::class)->middleware(['auth', 'role:admin|cashier'])->name('pos.shift');
Route::get('/pos/history', HistoryPage::class)->middleware(['auth', 'role:admin|cashier'])->name('pos.history');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/barcode/{product}', [App\Http\Controllers\BarcodeController::class, 'generate'])->name('barcode.generate');
Route::get('/qris/{transactionId}', [App\Http\Controllers\QrCodeController::class, 'generate'])->name('qris.image');
Route::get('/payment/{payment}/status', [App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('payment.status');

Route::middleware(['auth', 'role:admin'])->get('/admin/barcodes/print', function () {
    $products = App\Models\Product::where('is_active', true)->get();
    return view('admin.barcodes.print', compact('products'));
})->name('admin.barcodes.print');

require __DIR__ . '/auth.php';
