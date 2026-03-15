<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoldPriceDaily;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GoldCandlestickController extends Controller
{
    /**
     * Return 30 days of candlestick data ending at yesterday.
     *
     * Each item contains:
     *  - timestamp : Unix time in milliseconds, used as x-axis value in Flutter fl_chart
     *  - date      : Human-readable date (Y-m-d)
     *  - open      : Opening price  — maps directly to CandlestickSpot.open  in fl_chart
     *  - close     : Closing price  — maps directly to CandlestickSpot.close in fl_chart
     *  - high      : Highest price  — maps directly to CandlestickSpot.high  in fl_chart
     *  - low       : Lowest price   — maps directly to CandlestickSpot.low   in fl_chart
     */
    public function candlestick(): JsonResponse
    {
        try {
            $yesterday = Carbon::yesterday()->toDateString();
            $startDate = Carbon::yesterday()->subDays(29)->toDateString();

            $records = GoldPriceDaily::select([
                'gold_price_date',
                'open_price',
                'close_price',
                'highest_price',
                'lowest_price',
            ])
                ->whereBetween('gold_price_date', [$startDate, $yesterday])
                ->orderBy('gold_price_date', 'asc')
                ->get();

            if ($records->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No gold candlestick data found',
                    'data'    => [],
                ]);
            }

            $data = $records->map(function ($row) {
                // gold_price_date is cast as Carbon by the model
                $date = $row->gold_price_date;

                return [
                    'timestamp' => $date->startOfDay()->getTimestampMs(),
                    'date'      => $date->format('Y-m-d'),
                    'open'      => (float) $row->open_price,
                    'close'     => (float) $row->close_price,
                    'high'      => (float) $row->highest_price,
                    'low'       => (float) $row->lowest_price,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Gold candlestick data retrieved successfully',
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('GoldCandlestickController: failed to retrieve data', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gold candlestick data',
            ], 500);
        }
    }
}
