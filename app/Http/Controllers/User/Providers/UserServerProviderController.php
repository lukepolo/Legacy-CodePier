<?php

namespace App\Http\Controllers\User\Providers;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;

/**
 * Class UserServerProviderController.
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
        return response(UserServerProvider::with('serverProvider')->where('user_id', \Auth::user()->id)->get());
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
        return response(UserServerProvider::with('serverProvider')->findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        UserServerProvider::findOrFail($id)->delete();
    }
}
