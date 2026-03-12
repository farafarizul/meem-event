<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventCheckinController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GoldPriceController;
use App\Http\Controllers\Admin\GoldPriceSyncSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\CheckinController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public check-in routes (no auth required)
Route::prefix('checkin')->name('checkin.')->group(function () {
    // QR code scan flow: /checkin?scannedfromapp=EVENT-XXX&user_id=OBFUSCATED
    Route::get('/', [CheckinController::class, 'showByQR'])->name('qr.show');
    Route::post('/', [CheckinController::class, 'storeByQR'])->name('qr.store');

    // Legacy list-based flow: /checkin/{uniqueIdentifier}
    Route::get('/{uniqueIdentifier}', [CheckinController::class, 'show'])->name('show');
    Route::post('/{uniqueIdentifier}', [CheckinController::class, 'store'])->name('store');
});

// Admin routes – require authentication + admin role
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {

    // Branches (CRUD + soft delete)
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::get('/branches/datatable', [BranchController::class, 'datatable'])->name('branches.datatable');
    Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users (listing + edit/delete + export)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Events (CRUD + QR + export)
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/datatable', [EventController::class, 'datatable'])->name('events.datatable');
    Route::get('/events/export', [EventController::class, 'export'])->name('events.export');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}/qr-download', [EventController::class, 'qrDownload'])->name('events.qr-download');

    // Check-ins (listing by event + delete + export)
    Route::get('/checkins', [EventCheckinController::class, 'index'])->name('checkins.index');
    Route::get('/checkins/datatable', [EventCheckinController::class, 'datatable'])->name('checkins.datatable');
    Route::get('/checkins/export', [EventCheckinController::class, 'export'])->name('checkins.export');
    Route::delete('/checkins/{checkin}', [EventCheckinController::class, 'destroy'])->name('checkins.destroy');

    // Logs
    Route::get('/logs', [EventCheckinController::class, 'index'])->name('logs.index');

    // Gold Price History
    Route::get('/gold-price', [GoldPriceController::class, 'index'])->name('gold-price.index');
    Route::get('/gold-price/datatable', [GoldPriceController::class, 'datatable'])->name('gold-price.datatable');
    Route::post('/gold-price/sync-now', [GoldPriceController::class, 'syncNow'])->name('gold-price.sync-now');

    // Gold Price Sync Settings
    Route::get('/settings/gold-price-sync', [GoldPriceSyncSettingController::class, 'index'])->name('gold-price.settings');
    Route::post('/settings/gold-price-sync', [GoldPriceSyncSettingController::class, 'update'])->name('gold-price.settings.update');
});

require __DIR__ . '/auth.php';
