<?php

namespace App\Http\Controllers\User\Providers;

use App\Http\Controllers\Controller;
use App\Models\NotificationProvider;
use App\Models\User\UserNotificationProvider;
use App\Http\Requests\User\UserNotificationProviderConnectionRequest;

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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(UserNotificationProvider::findOrFail($id));
    }

    /**
     * Store a user notification provider token
     *
     * @param string $provider
     * @param \App\Http\Requests\User\UserNotificationProviderConnectionRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function connect($provider, UserNotificationProviderConnectionRequest $request)
    {
        $newProvider = UserNotificationProvider::withTrashed()->firstOrCreate([
            'notification_provider_id' => NotificationProvider::where('provider_name', $provider)->firstOrFail()->id,
            'user_id' => $request->user()->id,
        ], ['token' => $request->token]);
        
        $newProvider->restore();

        return $newProvider;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return bool|null
     * @throws \Exception
     */
    public function destroy($id)
    {
        return response()->json(UserNotificationProvider::findOrFail($id)->delete());
    }
}
