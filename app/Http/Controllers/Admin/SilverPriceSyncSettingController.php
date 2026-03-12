<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SilverPriceService;
use Illuminate\Http\Request;

class SilverPriceSyncSettingController extends Controller
{
    public function __construct(private SilverPriceService $service) {}

    public function index()
    {
        $currentInterval = $this->service->getSyncIntervalMinutes();

        return view('admin.silver-price.settings', compact('currentInterval'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'interval_minutes' => 'required|integer|in:5,10,15,30',
        ]);

        $this->service->setSyncIntervalMinutes((int) $request->interval_minutes);

        return redirect()->route('admin.silver-price.settings')
            ->with('success', 'Sync interval updated to ' . $request->interval_minutes . ' minutes.');
    }
}
