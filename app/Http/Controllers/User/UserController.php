<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;

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
     * @param UserUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id = null)
    {
        $user = empty($id) ? \Auth::user() : User::findOrFail($id);

        $user->fill([
            'name'  => $request->get('name'),
            'email' => $request->get('email'),
        ]);

        if ($request->has('password')) {
            $user->password = \Hash::make($request->get('password'));
        }

        $user->save();

        return response()->json($user->load(['currentTeam', 'currentPile']));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRunningCommands()
    {
        return response()->json(

        );
    }
}
