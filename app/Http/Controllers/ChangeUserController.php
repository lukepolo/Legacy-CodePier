<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ChangeUserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        $request->session()->push('is_acting', \Auth::user()->id);
        \Auth::loginUsingId($userId);
        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        \Auth::loginUsingId($request->session()->pull('is_acting'));
        return redirect()->back();
    }
}
