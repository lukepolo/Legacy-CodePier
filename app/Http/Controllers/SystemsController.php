<?php

namespace App\Http\Controllers;

use App\Models\System;

class SystemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(System::all());
    }
}
