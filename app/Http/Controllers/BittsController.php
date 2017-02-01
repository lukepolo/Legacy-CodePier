<?php

namespace App\Http\Controllers;

use App\Models\Bitt;
use App\Http\Requests\BittRequest;

class BittsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Bitt::paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BittRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BittRequest $request)
    {
        Bitt::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->get('name'),
            'script' => $request->get('script'),
            'system' => $request->get('system'),
            'version' => $request->get('version'),
        ]);

        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Bitt::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BittRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(BittRequest $request, $id)
    {
        $bitt = Bitt::findOrFail($id);

        $bitt->update([
            'name' => $request->get('name'),
            'script' => $request->get('script'),
            'system' => $request->get('system'),
            'version' => $request->get('version'),
        ]);

        return response()->json($bitt);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(Bitt::findOrFail($id)->delete());
    }
}
