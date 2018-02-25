<?php

namespace App\Http\Controllers\Auth\Providers;

use App\Models\RepositoryProvider;
use App\Http\Controllers\Controller;

class RepositoryProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(RepositoryProvider::all());
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
        return response(RepositoryProvider::findOrFail($id));
    }
}
