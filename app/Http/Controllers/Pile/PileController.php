<?php

namespace App\Http\Controllers\Pile;

use App\Http\Controllers\Controller;
use App\Models\Pile;
use Illuminate\Http\Request;

/**
 * Class PileController
 *
 * @package App\Http\Controllers\Server
 */
class PileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Pile::with('servers')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pile = Pile::create([
            'name' => $request->get('name')
        ]);

        return response()->json($pile);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pile = Pile::findOrFail($id);
        $pile->fill([
            'user_id' => \Auth::user()->id,
            'name' => $request->get('name')
        ]);

        $pile->save();

        return response()->json($pile);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Pile::with('servers')->findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Pile::findOrFail($id)->delete();
    }
}
