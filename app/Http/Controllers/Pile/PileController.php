<?php

namespace App\Http\Controllers\Pile;

use App\Models\Pile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pile\PileRequest;

class PileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Pile::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PileRequest $request)
    {
        $pile = Pile::create([
            'user_id' => \Auth::user()->id,
            'name'    => $request->get('name'),
        ]);

        return response()->json($pile);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PileRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PileRequest $request, $id)
    {
        $pile = Pile::findOrFail($id);

        $pile->update([
            'user_id' => \Auth::user()->id,
            'name'    => $request->get('name'),
        ]);

        return response()->json($pile);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Pile::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Pile::findOrFail($id)->delete();
    }

    /**
     * Changes the users pile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePile(Request $request)
    {
        $user = \Auth::user();

        $user->current_pile_id = $request->get('pile');
        $user->save();

        return response()->json($user->load(['currentTeam', 'currentPile']));
    }

    public function allPiles()
    {
        return response()->json(Pile::allTeams()->get());
    }
}
