<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function termsOfService()
    {
        return view('terms');
    }
}
