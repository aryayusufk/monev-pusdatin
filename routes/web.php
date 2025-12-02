<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\IkuController;
use App\Http\Controllers\ActivityFileController;
use App\Http\Controllers\ActivityNoteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\ConfigurationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Semua route yang butuh login
Route::middleware('auth')->group(function () {

    Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Activities (Laporan Kegiatan)
    |--------------------------------------------------------------------------
    */
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Seleksi triwulan
    Route::post('/activities/{activity}/select-quarterly', [ActivityController::class, 'selectQuarterly'])
        ->name('activities.select-quarterly');

    /*
    |--------------------------------------------------------------------------
    | Activity Files (Data Pendukung / Lampiran PDF)
    |--------------------------------------------------------------------------
    */
    Route::post('/activities/{activity}/files', [ActivityFileController::class, 'store'])
        ->name('activities.files.store');

    Route::get('/files/{file}/download', [ActivityFileController::class, 'download'])
        ->name('files.download');

    Route::delete('/files/{file}', [ActivityFileController::class, 'destroy'])
        ->name('files.destroy');

    /*
    |--------------------------------------------------------------------------
    | Activity Notes (Catatan Monev)
    |--------------------------------------------------------------------------
    */
    Route::post('/activities/{activity}/notes', [ActivityNoteController::class, 'store'])
        ->name('activities.notes.store');

    Route::delete('/notes/{note}', [ActivityNoteController::class, 'destroy'])
        ->name('notes.destroy');

    /*
    |--------------------------------------------------------------------------
    | IKU Management
    |--------------------------------------------------------------------------
    */
    Route::get('/ikus', [IkuController::class, 'index'])->name('ikus.index');
    Route::get('/ikus/create', [IkuController::class, 'create'])->name('ikus.create');
    Route::post('/ikus', [IkuController::class, 'store'])->name('ikus.store');
    Route::get('/ikus/{iku}/edit', [IkuController::class, 'edit'])->name('ikus.edit');
    Route::put('/ikus/{iku}', [IkuController::class, 'update'])->name('ikus.update');
    Route::patch('/ikus/{iku}/toggle-active', [IkuController::class, 'toggleActive'])->name('ikus.toggle-active');

    /*
    |--------------------------------------------------------------------------
    | Bidang Management + Pembuatan User Bidang & Monev Bidang
    |--------------------------------------------------------------------------
    | Hanya super_admin yang bisa akses, dicek di BidangController::ensureCanManageBidangs()
    */
    Route::get('/bidangs', [BidangController::class, 'index'])->name('bidangs.index');
    Route::get('/bidangs/create', [BidangController::class, 'create'])->name('bidangs.create');
    Route::post('/bidangs', [BidangController::class, 'store'])->name('bidangs.store');
    Route::get('/bidangs/{bidang}/edit', [BidangController::class, 'edit'])->name('bidangs.edit');
    Route::put('/bidangs/{bidang}', [BidangController::class, 'update'])->name('bidangs.update');
    Route::delete('/bidangs/{bidang}', [BidangController::class, 'destroy'])->name('bidangs.destroy');

    /*
    |--------------------------------------------------------------------------
    | User Management (Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
