<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    public function allFeatures()
    {
        return view('all-features');
    }

    public function changeLog()
    {
        return view('change-log');
    }

    public function faq()
    {
        return view('faq');
    }

    public function termsOfService()
    {
        return view('terms');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function roadmap()
    {
        return view('roadmap');
    }

    public function styleGuide()
    {
        return view('style-guide.index');
    }
}
