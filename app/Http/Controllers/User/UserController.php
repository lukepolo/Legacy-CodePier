<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Auth::user()->load(['currentTeam', 'currentPile']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        $user = empty($id) ? \Auth::user() : User::findOrFail($id);

        $user->fill([
            'name'  => \Request::get('name'),
            'email' => \Request::get('email'),
        ]);

        if (\Request::has('password')) {
            $user->password = \Hash::make(\Request::get('password'));
        }

        $user->save();

        return response()->json($user->load(['currentTeam', 'currentPile']));
    }

    public function getRunningCommands()
    {
        return response()->json(

        );
    }
}
