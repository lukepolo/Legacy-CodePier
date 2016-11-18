<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;

class NotificationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return response()->json(NotificationSetting::all());
    }
}
