<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Models\SilverPrice;
use Illuminate\Http\JsonResponse;

class GoldSilverPriceController extends Controller
{
    public function goldPrice(): JsonResponse
    {
        $record = GoldPrice::orderByDesc('last_updated')->first();

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'No gold price data available.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatPrice($record),
        ]);
    }

    public function silverPrice(): JsonResponse
    {
        $record = SilverPrice::orderByDesc('last_updated')->first();

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'No silver price data available.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatPrice($record),
        ]);
    }

    public function goldAndSilverPrice(): JsonResponse
    {
        $gold   = GoldPrice::orderByDesc('last_updated')->first();
        $silver = SilverPrice::orderByDesc('last_updated')->first();

        if (! $gold && ! $silver) {
            return response()->json([
                'success' => false,
                'message' => 'No gold or silver price data available.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'gold'   => $gold   ? $this->formatPrice($gold)   : null,
                'silver' => $silver ? $this->formatPrice($silver) : null,
            ],
        ]);
    }

    private function formatPrice(GoldPrice|SilverPrice $record): array
    {
        return [
            'type'         => $record->type,
            'product'      => $record->product,
            'unit'         => $record->unit,
            'currency'     => $record->currency,
            'sell_price'   => number_format((float) $record->sell_price, 2, '.', ''),
            'buy_price'    => number_format((float) $record->buy_price, 2, '.', ''),
            'timezone'     => $record->timezone,
            'last_updated' => $record->last_updated->format('d-m-Y H:i:s'),
        ];
    }
}
