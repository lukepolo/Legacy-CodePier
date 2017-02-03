<?php

namespace App\Http\Controllers;

use App\Models\Bitt;
use Illuminate\Http\Request;
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
        $bitt = Bitt::create([
            'user_id' => \Auth::user()->id,
            'title' => $request->get('title'),
            'script' => $request->get('script'),
            'private' => $request->get('private'),
            'description' => $request->get('description'),
        ]);

        $bitt->systems()->sync($request->get('systems'));
        $bitt->categories()->sync((array) $request->get('category'));

        $bitt->fresh(['systems', 'categories']);

        return response()->json($bitt);
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
            'title' => $request->get('title'),
            'script' => $request->get('script'),
            'private' => $request->get('private'),
            'description' => $request->get('description'),
        ]);

        $bitt->systems()->sync($request->get('systems'));
        $bitt->categories()->sync((array) $request->get('category'));

        $bitt->fresh(['systems', 'categories']);

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

    /**
     * @param Request $request
     * @param $bit
     */
    public function runOnServers(Request $request, $bit)
    {
        dd($request->all());
    }
}
