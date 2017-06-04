<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Services\Systems\SystemService;

class ServerTypesController extends Controller
{
    /**
     * Gets the server types that we are able to provision.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            SystemService::SERVER_TYPES
        );
    }
}
