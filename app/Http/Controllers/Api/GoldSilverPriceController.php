<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\Far_log;
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

        $returnData = [
            'gold'   => $gold   ? $this->formatPrice($gold)   : null,
            'silver' => $silver ? $this->formatPrice($silver) : null,
        ];

        //get parameter named meem_code from the url
        $meem_code = request()->query('meem_code', 'unknown');

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerClass = $trace[1]['class'] ?? null;
        // Jika pemanggil adalah Controller (bukan sistem Laravel/Router)
        // bermaksud ia dipanggil dari controller lain
        $isInternalCall = $callerClass && str_contains($callerClass, 'App\Http\Controllers');

        if (!$isInternalCall) {
            // Hanya simpan log jika dipanggil secara direct (melalui Route)
            Far_log::insert_userlog(1, 'api', 'price', 'gold_and_silver_price' ,$returnData, $meem_code);
        }



        return response()->json([
            'success' => true,
            'data'    => $returnData,
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
