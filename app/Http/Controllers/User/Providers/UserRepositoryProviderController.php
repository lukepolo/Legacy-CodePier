<?php

namespace App\Http\Controllers\User\Providers;

use App\Http\Controllers\Controller;
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
        return response(UserRepositoryProvider::with('repositoryProvider')->where('user_id', \Auth::user()->id)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param $userId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId, $id)
    {
        return response(UserRepositoryProvider::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id)
    {
        UserRepositoryProvider::findOrFail($id)->delete();
    }
}
