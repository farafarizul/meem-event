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
use App\Http\Controllers\Admin\ListCountryController;
use App\Http\Controllers\Admin\ListIndustryController;
use App\Http\Controllers\Admin\ListStateController;
use App\Http\Controllers\Admin\PushNotificationController;
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
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/{user}/tab/basic-info', [UserController::class, 'tabBasicInfo'])->name('users.tab.basic-info');
    Route::get('/users/{user}/tab/events', [UserController::class, 'tabEvents'])->name('users.tab.events');
    Route::get('/users/{user}/tab/logs', [UserController::class, 'tabLogs'])->name('users.tab.logs');
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

    // Push Notifications
    Route::get('/push-notifications', [PushNotificationController::class, 'index'])->name('push-notifications.index');
    Route::post('/push-notifications', [PushNotificationController::class, 'store'])->name('push-notifications.store');
    Route::get('/push-notifications/datatable', [PushNotificationController::class, 'datatable'])->name('push-notifications.datatable');
    Route::get('/push-notifications/users/search', [PushNotificationController::class, 'usersSearch'])->name('push-notifications.users.search');
    Route::get('/push-notifications/{pushNotification}', [PushNotificationController::class, 'show'])->name('push-notifications.show');

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

    // Settings — List of States
    Route::get('/settings/list-states', [ListStateController::class, 'index'])->name('settings.list-states.index');
    Route::get('/settings/list-states/datatable', [ListStateController::class, 'datatable'])->name('settings.list-states.datatable');
    Route::post('/settings/list-states/sync', [ListStateController::class, 'sync'])->name('settings.list-states.sync');

    // Settings — List of Countries
    Route::get('/settings/list-countries', [ListCountryController::class, 'index'])->name('settings.list-countries.index');
    Route::get('/settings/list-countries/datatable', [ListCountryController::class, 'datatable'])->name('settings.list-countries.datatable');
    Route::post('/settings/list-countries/sync', [ListCountryController::class, 'sync'])->name('settings.list-countries.sync');

    // Settings — List of Industries
    Route::get('/settings/list-industries', [ListIndustryController::class, 'index'])->name('settings.list-industries.index');
    Route::get('/settings/list-industries/datatable', [ListIndustryController::class, 'datatable'])->name('settings.list-industries.datatable');
    Route::post('/settings/list-industries/sync', [ListIndustryController::class, 'sync'])->name('settings.list-industries.sync');
});

require __DIR__ . '/auth.php';
