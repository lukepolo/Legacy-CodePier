<?php

namespace App\Http\Controllers;

use App\Models\UserNotificationProvider;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserNotificationProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(UserNotificationProvider::where('user_id', \Auth::user()->id)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(UserNotificationProvider::findOrFail($id));
    }
}
