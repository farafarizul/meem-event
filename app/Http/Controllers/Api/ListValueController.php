<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListValueController extends Controller
{
    private const ALLOWED_COUNTRIES = ['Brunei', 'Indonesia', 'Malaysia', 'Singapore', 'Thailand'];

    public function index(): JsonResponse
    {
        try {
            $states = DB::table('list_states')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            $countries = DB::table('list_countries')
                ->select('id', 'name')
                ->whereIn('name', self::ALLOWED_COUNTRIES)
                ->orderBy('name', 'asc')
                ->get();

            $industries = DB::table('list_industries')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            $identificationTypes = DB::table('list_identification_type')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => [
                    'list_states'              => $states,
                    'list_countries'           => $countries,
                    'list_industries'          => $industries,
                    'list_identification_type' => $identificationTypes,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ListValueController@index failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load list values',
            ], 500);
        }
    }
}
