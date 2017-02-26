<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function termsOfService()
    {
        return view('terms');
    }

    public function privacy()
    {
        return view('privacy');
    }
}
