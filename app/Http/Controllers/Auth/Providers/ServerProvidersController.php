<?php

namespace App\Http\Controllers\Auth\Providers;

use App\Http\Controllers\Controller;
use App\Models\ServerProvider;
use App\Models\UserServerProvider;

/**
 * Class ServerProvidersController
 * @package App\Http\Controllers
 */
class ServerProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(ServerProvider::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(ServerProvider::findOrFail($id));
    }
}
