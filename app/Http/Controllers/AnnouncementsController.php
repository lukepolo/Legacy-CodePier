<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class AnnouncementsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $user = \Auth::user();

        $user->update([
            'last_read_announcement' => Carbon::now(),
        ]);

        return response()->json($user);
    }
}
