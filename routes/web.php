<?php

use App\Http\Controllers\Admin\ApkDetailController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventCheckinController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GoldPriceController;
use App\Http\Controllers\Admin\GoldPriceDailyController;
use App\Http\Controllers\Admin\GoldPriceSyncSettingController;
use App\Http\Controllers\Admin\SilverPriceController;
use App\Http\Controllers\Admin\SilverPriceDailyController;
use App\Http\Controllers\Admin\SilverPriceSyncSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\CheckinController;
use Illuminate\Support\Facades\Route;

// Public APK download (no auth required)
Route::get('/apk/download/{apkDetail}', [ApkDetailController::class, 'download'])->name('apk.download');

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
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/datatable', [LogController::class, 'datatable'])->name('logs.datatable');

    // Gold Price History
    Route::get('/gold-price', [GoldPriceController::class, 'index'])->name('gold-price.index');
    Route::get('/gold-price/datatable', [GoldPriceController::class, 'datatable'])->name('gold-price.datatable');
    Route::post('/gold-price/sync-now', [GoldPriceController::class, 'syncNow'])->name('gold-price.sync-now');

    // Gold Price Sync Settings
    Route::get('/settings/gold-price-sync', [GoldPriceSyncSettingController::class, 'index'])->name('gold-price.settings');
    Route::post('/settings/gold-price-sync', [GoldPriceSyncSettingController::class, 'update'])->name('gold-price.settings.update');

    // Silver Price History
    Route::get('/silver-price', [SilverPriceController::class, 'index'])->name('silver-price.index');
    Route::get('/silver-price/datatable', [SilverPriceController::class, 'datatable'])->name('silver-price.datatable');
    Route::post('/silver-price/sync-now', [SilverPriceController::class, 'syncNow'])->name('silver-price.sync-now');

    // APK File Management
    Route::get('/apk-detail', [ApkDetailController::class, 'index'])->name('apk-detail.index');
    Route::get('/apk-detail/create', [ApkDetailController::class, 'create'])->name('apk-detail.create');
    Route::post('/apk-detail', [ApkDetailController::class, 'store'])->name('apk-detail.store');
    Route::get('/apk-detail/datatable', [ApkDetailController::class, 'datatable'])->name('apk-detail.datatable');
    Route::delete('/apk-detail/{apkDetail}', [ApkDetailController::class, 'destroy'])->name('apk-detail.destroy');

    // Silver Price Sync Settings
    Route::get('/settings/silver-price-sync', [SilverPriceSyncSettingController::class, 'index'])->name('silver-price.settings');
    Route::post('/settings/silver-price-sync', [SilverPriceSyncSettingController::class, 'update'])->name('silver-price.settings.update');

    // Gold Price Daily (daily summaries + AI reason)
    Route::get('/gold-price-daily', [GoldPriceDailyController::class, 'index'])->name('gold-price-daily.index');
    Route::get('/gold-price-daily/datatable', [GoldPriceDailyController::class, 'datatable'])->name('gold-price-daily.datatable');
    Route::get('/gold-price-daily/{goldPriceDaily}', [GoldPriceDailyController::class, 'show'])->name('gold-price-daily.show');
    Route::post('/gold-price-daily/manual-sync', [GoldPriceDailyController::class, 'manualSync'])->name('gold-price-daily.manual-sync');
    Route::post('/gold-price-daily/{goldPriceDaily}/sync-this', [GoldPriceDailyController::class, 'syncThis'])->name('gold-price-daily.sync-this');
    Route::post('/gold-price-daily/{goldPriceDaily}/regen-ai', [GoldPriceDailyController::class, 'regenAi'])->name('gold-price-daily.regen-ai');

    // Silver Price Daily (daily summaries + AI reason)
    Route::get('/silver-price-daily', [SilverPriceDailyController::class, 'index'])->name('silver-price-daily.index');
    Route::get('/silver-price-daily/datatable', [SilverPriceDailyController::class, 'datatable'])->name('silver-price-daily.datatable');
    Route::post('/silver-price-daily/manual-sync', [SilverPriceDailyController::class, 'manualSync'])->name('silver-price-daily.manual-sync');
    Route::get('/silver-price-daily/{silverPriceDaily}', [SilverPriceDailyController::class, 'show'])->name('silver-price-daily.show');
    Route::post('/silver-price-daily/{silverPriceDaily}/sync-this', [SilverPriceDailyController::class, 'syncThis'])->name('silver-price-daily.sync-this');
    Route::post('/silver-price-daily/{silverPriceDaily}/regen-ai', [SilverPriceDailyController::class, 'regenAi'])->name('silver-price-daily.regen-ai');
});

require __DIR__ . '/auth.php';
