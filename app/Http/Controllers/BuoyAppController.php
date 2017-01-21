<?php

namespace App\Http\Controllers;

use App\Models\BuoyApp;
use App\Http\Requests\BuoyRequest;

class BuoyAppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(BuoyApp::paginate(25));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BuoyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BuoyRequest $request)
    {
        return response()->json(BuoyApp::create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(BuoyApp::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BuoyRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BuoyRequest $request, $id)
    {
        $buoy = BuoyApp::findOrFail($id);

        $buoy->update($request->all());

        return response()->json($buoy);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(BuoyApp::destroy($id));
    }
}
