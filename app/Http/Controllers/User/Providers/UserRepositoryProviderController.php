<?php

namespace App\Http\Controllers;

use App\Models\UserRepositoryProvider;

/**
 * Class UserRepositoryProviderController
 * @package App\Http\Controllers
 */
class UserRepositoryProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(UserRepositoryProvider::where('user_id', \Auth::user()->id)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(UserRepositoryProvider::findOrFail($id));
    }
}
