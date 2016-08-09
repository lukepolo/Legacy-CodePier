<?php

namespace App\Http\Controllers;

use App\Models\UserServerProvider;

/**
 * Class UserServerProviderController
 * @package App\Http\Controllers
 */
class UserServerProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(UserServerProvider::where('user_id', \Auth::user()->id)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(UserServerProvider::findOrFail($id));
    }
}
