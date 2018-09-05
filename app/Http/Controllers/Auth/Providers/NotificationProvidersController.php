<?php

namespace App\Http\Controllers\Auth\Providers;

use App\Http\Controllers\Controller;
use App\Models\NotificationProvider;

class NotificationProvidersController extends Controller
{
    const OAUTH = 'oauth';
    const WEBHOOK = 'webhook';
    const DISCORD = 'discord';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(NotificationProvider::all());
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
        return response(NotificationProvider::findOrFail($id));
    }
}
