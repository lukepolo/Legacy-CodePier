<?php

namespace App\Http\Controllers;

use App\Models\BetaEmail;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            BetaEmail::create([
                'email' => $request->get('email'),
            ]);
        } catch (QueryException $e) {
            Session::put('registered_for_beta', true);

            return back()
                ->cookie('registered_for_beta', true);
        }

        Session::put('registered_for_beta', true);

        return back()->cookie('registered_for_beta', true);
    }

    public function styleGuide()
    {
        return view('style-guide.index');
    }
}
