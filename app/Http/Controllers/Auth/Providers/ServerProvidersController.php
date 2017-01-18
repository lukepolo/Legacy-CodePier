<?php

namespace App\Http\Controllers\Auth\Providers;

use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;
use App\Services\Server\Providers\CustomProvider;

class ServerProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(ServerProvider::where('provider_class', '!=', CustomProvider::class)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(ServerProvider::findOrFail($id));
    }
}
