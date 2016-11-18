<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserNotificationSetting;
use Illuminate\Http\Request;

class UserNotificationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Auth::user()->notificationSettings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        foreach ($request->get('notification_setting') as $notificationSettingId => $services) {
            $userNotification = UserNotificationSetting::firstOrNew([
                'user_id' => \Auth::user()->id,
                'notification_setting_id' => $notificationSettingId,
            ]);

            $userNotification->fill([
                'services' => array_keys($services),
            ]);

            $userNotification->save();
        }

        return response()->json('OK');
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
        //        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
    }
}
