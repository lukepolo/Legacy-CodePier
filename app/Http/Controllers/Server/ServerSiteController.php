<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Http\Controllers\Controller;

class ServerSiteController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return response()->json(
            Server::with(['sites' => function ($query) {
                $query->with([
                    'activeSSL',
                    'workers',
                    'userRepositoryProvider',
                ]);
            }])->findOrFail($id)->sites
        );
    }
}
