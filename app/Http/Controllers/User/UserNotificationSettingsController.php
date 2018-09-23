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
        $userNotification = UserNotificationSetting::firstOrNew([
            'user_id' => \Auth::user()->id,
            'notification_setting_id' => $request->get('notification_setting_id'),
        ]);

        $userNotification->services = $request->get('services');

        $userNotification->save();

        return response()->json(\Auth::user()->notificationSettings);
    }
}
