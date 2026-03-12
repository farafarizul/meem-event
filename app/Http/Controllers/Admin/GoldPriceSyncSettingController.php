<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;

class GoldPriceSyncSettingController extends Controller
{
    public function __construct(private GoldPriceService $service) {}

    public function index()
    {
        $currentInterval = $this->service->getSyncIntervalMinutes();

        return view('admin.gold-price.settings', compact('currentInterval'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'interval_minutes' => 'required|integer|in:5,10,15,30',
        ]);

        $this->service->setSyncIntervalMinutes((int) $request->interval_minutes);

        return redirect()->route('admin.gold-price.settings')
            ->with('success', 'Sync interval updated to ' . $request->interval_minutes . ' minutes.');
    }
}
