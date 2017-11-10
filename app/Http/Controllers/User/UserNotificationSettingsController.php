<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\UserNotificationSetting;

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

            $userNotification->services = collect($services)->filter(function ($item) {
                return $item;
            })->keys();

            $userNotification->save();
        }

        return response()->json(\Auth::user()->notificationSettings);
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
