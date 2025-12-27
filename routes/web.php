<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityFileController;
use App\Http\Controllers\ActivityNoteController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\IkuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root ke halaman login
Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bulk actions untuk menambahkan/menghapus kegiatan ke/dari laporan monev
    Route::post('/activities/bulk-add-monev', [ActivityController::class, 'bulkAddToMonev'])
        ->name('activities.bulk-add-monev');

    Route::post('/activities/bulk-remove-monev', [ActivityController::class, 'bulkRemoveFromMonev'])
        ->name('activities.bulk-remove-monev');

    // Laporan Kegiatan (Activities)
    Route::resource('activities', ActivityController::class)->except(['show']);

    Route::post('/activities/{activity}/select-quarterly', [ActivityController::class, 'selectQuarterly'])
        ->name('activities.select-quarterly');

    // Laporan Bidang (mingguan/bulanan)
    Route::prefix('laporan-bidang')->name('reports.bidang.')->group(function () {
        Route::get('/mingguan', [ActivityController::class, 'weeklyIndex'])->name('index');
        Route::get('/mingguan/{year}/{week}', [ActivityController::class, 'weeklyReport'])->name('weekly.report');
        Route::get('/bulanan/{year}/{month}', [ActivityController::class, 'monthlyReport'])->name('monthly.report');
    });

    // Laporan Monitoring & Evaluasi
    Route::get('/laporan-monitoring-evaluasi', [ActivityController::class, 'monevDashboard'])
        ->name('reports.monev.index');

    Route::get('/laporan-monitoring-evaluasi/activities', [ActivityController::class, 'monevIndex'])
        ->name('reports.monev.activities');

    Route::get('/laporan-monitoring-evaluasi/export/weekly', [ActivityController::class, 'monevWeeklyExportRange'])
        ->name('reports.monev.export.weekly');

    Route::get('/laporan-monitoring-evaluasi/export/monthly', [ActivityController::class, 'monevMonthlyExportRange'])
        ->name('reports.monev.export.monthly');

    Route::get('/laporan-monitoring-evaluasi/export/quarterly', [ActivityController::class, 'monevQuarterlyExportRange'])
        ->name('reports.monev.export.quarterly');

    // Export PDF laporan bidang
    Route::prefix('reports/bidang/export')->name('reports.bidang.export.')->group(function () {
        Route::get('/weekly', [ActivityController::class, 'weeklyExportRange'])->name('weekly');
        Route::get('/monthly', [ActivityController::class, 'monthlyExportRange'])->name('monthly');
        Route::get('/quarterly', [ActivityController::class, 'quarterlyExportRange'])->name('quarterly');
    });

    // Lampiran file kegiatan
    Route::post('/activities/{activity}/files', [ActivityFileController::class, 'store'])
        ->name('activities.files.store');

    Route::get('/files/{file}/download', [ActivityFileController::class, 'download'])
        ->name('files.download');

    Route::delete('/files/{file}', [ActivityFileController::class, 'destroy'])
        ->name('files.destroy');

    // Catatan monev
    Route::post('/activities/{activity}/notes', [ActivityNoteController::class, 'store'])
        ->name('activities.notes.store');

    Route::delete('/notes/{note}', [ActivityNoteController::class, 'destroy'])
        ->name('notes.destroy');

    // IKU
    Route::resource('ikus', IkuController::class);

    Route::patch('/ikus/{iku}/toggle-active', [IkuController::class, 'toggleActive'])
        ->name('ikus.toggle-active');

    // Bidang (khusus super_admin, dicek di controller)
    Route::resource('bidangs', BidangController::class);

    // User (super_admin)
    Route::resource('users', UserController::class);

    // Logs
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
});

require __DIR__ . '/auth.php';
