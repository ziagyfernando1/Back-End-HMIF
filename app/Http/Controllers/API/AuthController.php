<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function nimChecking($nim)
    {
        try {
            if (is_null($nim)) {
                return ApiFormatter::createApi(400, 'NIM is invalid');
            }

            $apiUrl = env('AMIKOM_VALIDATION_API_URL');
            $userAgent = env('AMIKOM_USER_AGENT');

            $apiResponse = Http::withUserAgent($userAgent)
                ->asForm()
                ->post($apiUrl, [
                    'mhs_npm' => $nim,
                ])->json();

            $isAktif = $apiResponse['IsAktif'] ?? null;

            if ($isAktif) {
                return ApiFormatter::createApi(200, 'Student is Active', $apiResponse);
            } else {
                return APIFormatter::createApi(400, 'Student is Inactive', $apiResponse);
            }
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }
}
