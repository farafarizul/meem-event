<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    private const FIELDS = [
        'name',
        'identification_type_id',
        'identification_no',
        'email',
        'password',
        'password_confirmation',
        'contact_no',
        'occupation',
        'industry_id',
        'address_line_1',
        'city',
        'postcode',
        'state_id',
        'country_id',
        'chk_agree',
        'introducer_code',
    ];

    public function __construct(protected RegisterService $service) {}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'                   => 'required|string',
            'identification_type_id' => 'required|integer',
            'identification_no'      => 'required|string',
            'email'                  => 'required|email',
            'password'               => 'required|string',
            'password_confirmation'  => 'required|string',
            'contact_no'             => 'required|string',
            'occupation'             => 'required|string',
            'industry_id'            => 'required|integer',
            'address_line_1'         => 'required|string',
            'city'                   => 'required|string',
            'postcode'               => 'required|string',
            'state_id'               => 'required|integer',
            'country_id'             => 'required|string',
            'chk_agree'              => 'required|boolean',
            'introducer_code'        => 'nullable|string',
        ]);

        try {
            $upstream = $this->service->register($request->only(self::FIELDS));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Register upstream connection failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Registration service temporarily unavailable.',
            ], 502);
        }

        return response()->json($upstream->json(), $upstream->status());
    }
}
