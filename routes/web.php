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

    /*
    |--------------------------------------------------------------------------
    | Activities (Laporan Kegiatan)
    |--------------------------------------------------------------------------
    */

    Route::get('/activities', [ActivityController::class, 'index'])
        ->name('activities.index');

    Route::get('/activities/create', [ActivityController::class, 'create'])
        ->middleware('can:activities.create')
        ->name('activities.create');

    Route::post('/activities', [ActivityController::class, 'store'])
        ->middleware('can:activities.create')
        ->name('activities.store');

    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])
        ->name('activities.edit');

    Route::patch('/activities/{activity}', [ActivityController::class, 'update'])
        ->name('activities.update');

    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');

    // Bulk actions untuk sortir ke/dari laporan monev
    Route::post('/activities/bulk-add-monev', [ActivityController::class, 'bulkAddToMonev'])
        ->middleware('can:reports.sort')
        ->name('activities.bulk-add-monev');

    Route::post('/activities/bulk-remove-monev', [ActivityController::class, 'bulkRemoveFromMonev'])
        ->middleware('can:reports.sort')
        ->name('activities.bulk-remove-monev');

    Route::post('/activities/{activity}/select-quarterly', [ActivityController::class, 'selectQuarterly'])
        ->middleware('can:reports.sort')
        ->name('activities.select-quarterly');

    /*
    |--------------------------------------------------------------------------
    | Laporan Bidang
    |--------------------------------------------------------------------------
    | Dipakai oleh: super_admin, tim_monev, sekre_pimpinan, monev_bidang, user_bidang, pimpinan
    */
    Route::prefix('laporan-bidang')
        ->middleware('can:reports.bidang.view')
        ->name('reports.bidang.')
        ->group(function () {
            Route::get('/mingguan', [ActivityController::class, 'weeklyIndex'])->name('index');
            Route::get('/mingguan/{year}/{week}', [ActivityController::class, 'weeklyReport'])->name('weekly.report');
            Route::get('/bulanan/{year}/{month}', [ActivityController::class, 'monthlyReport'])->name('monthly.report');
        });

    // Export PDF laporan bidang
    Route::prefix('reports/bidang/export')
        ->middleware('can:reports.bidang.view')
        ->name('reports.bidang.export.')
        ->group(function () {
            Route::get('/weekly', [ActivityController::class, 'weeklyExportRange'])->name('weekly');
            Route::get('/monthly', [ActivityController::class, 'monthlyExportRange'])->name('monthly');
            Route::get('/quarterly', [ActivityController::class, 'quarterlyExportRange'])->name('quarterly');
        });

    /*
    |--------------------------------------------------------------------------
    | Laporan Monitoring & Evaluasi
    |--------------------------------------------------------------------------
    | Dipakai oleh: super_admin, tim_monev, sekre_pimpinan, monev_bidang
    */
    Route::prefix('laporan-monitoring-evaluasi')
        ->middleware('can:reports.monev.view')
        ->name('reports.monev.')
        ->group(function () {
            Route::get('/', [ActivityController::class, 'monevDashboard'])->name('index');
            Route::get('/activities', [ActivityController::class, 'monevIndex'])->name('activities');
            Route::get('/export/weekly', [ActivityController::class, 'monevWeeklyExportRange'])->name('export.weekly');
            Route::get('/export/monthly', [ActivityController::class, 'monevMonthlyExportRange'])->name('export.monthly');
            Route::get('/export/quarterly', [ActivityController::class, 'monevQuarterlyExportRange'])->name('export.quarterly');
        });

    /*
    |--------------------------------------------------------------------------
    | Lampiran file kegiatan
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
    | Catatan monev
    |--------------------------------------------------------------------------
    */
    Route::post('/activities/{activity}/notes', [ActivityNoteController::class, 'store'])
        ->name('activities.notes.store');

    Route::delete('/notes/{note}', [ActivityNoteController::class, 'destroy'])
        ->name('notes.destroy');

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi
    |--------------------------------------------------------------------------
    */

    // IKU (super_admin, tim_monev, sekre_pimpinan)
    Route::resource('ikus', IkuController::class)
        ->middleware('can:config.iku');

    Route::patch('/ikus/{iku}/toggle-active', [IkuController::class, 'toggleActive'])
        ->middleware('can:config.iku')
        ->name('ikus.toggle-active');

    // Bidang (super_admin)
    Route::resource('bidangs', BidangController::class)
        ->middleware('can:config.bidang');

    // User (super_admin)
    Route::resource('users', UserController::class)
        ->middleware('can:config.users');

    // Logs / Catatan Aktivitas (super_admin, tim_monev, sekre_pimpinan)
    Route::get('/logs', [SystemLogController::class, 'index'])
        ->middleware('can:activitylog.view')
        ->name('logs.index');
});

require __DIR__ . '/auth.php';
