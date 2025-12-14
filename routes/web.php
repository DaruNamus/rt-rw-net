<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PermintaanUpgradeController;
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;
use App\Http\Controllers\Pelanggan\TagihanController as PelangganTagihanController;
use App\Http\Controllers\Pelanggan\PembayaranController as PelangganPembayaranController;
use App\Http\Controllers\Pelanggan\PermintaanUpgradeController as PelangganPermintaanUpgradeController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Redirect dashboard berdasarkan role
Route::get('/', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('pelanggan.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('paket', PaketController::class);
    Route::resource('pelanggan', PelangganController::class);
    Route::resource('tagihan', TagihanController::class);
    Route::get('/tagihan/{tagihan}/cetak', [TagihanController::class, 'cetak'])->name('tagihan.cetak');
    
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{pembayaran}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pembayaran}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::post('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');
    Route::get('/pembayaran/{pembayaran}/cetak', [PembayaranController::class, 'cetak'])->name('pembayaran.cetak');
    
    Route::get('/permintaan-upgrade', [PermintaanUpgradeController::class, 'index'])->name('permintaan-upgrade.index');
    Route::get('/permintaan-upgrade/{permintaanUpgrade}', [PermintaanUpgradeController::class, 'show'])->name('permintaan-upgrade.show');
    Route::post('/permintaan-upgrade/{permintaanUpgrade}/setujui', [PermintaanUpgradeController::class, 'setujui'])->name('permintaan-upgrade.setujui');
    Route::post('/permintaan-upgrade/{permintaanUpgrade}/tolak', [PermintaanUpgradeController::class, 'tolak'])->name('permintaan-upgrade.tolak');
});

// Pelanggan Routes
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'verified', 'pelanggan'])->group(function () {
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/tagihan', [PelangganTagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{tagihan}', [PelangganTagihanController::class, 'show'])->name('tagihan.show');
    
    Route::get('/pembayaran', [PelangganPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create/{tagihan}', [PelangganPembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/{tagihan}', [PelangganPembayaranController::class, 'store'])->name('pembayaran.store');
    
    Route::get('/permintaan-upgrade', [PelangganPermintaanUpgradeController::class, 'index'])->name('permintaan-upgrade.index');
    Route::get('/permintaan-upgrade/create', [PelangganPermintaanUpgradeController::class, 'create'])->name('permintaan-upgrade.create');
    Route::post('/permintaan-upgrade', [PelangganPermintaanUpgradeController::class, 'store'])->name('permintaan-upgrade.store');
});

// Profile Routes (untuk semua user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
