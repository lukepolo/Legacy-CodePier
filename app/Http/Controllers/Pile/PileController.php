<?php

namespace App\Http\Controllers\Pile;

use App\Http\Controllers\Controller;
use App\Models\Pile;
use Illuminate\Http\Request;

/**
 * Class PileController.
 */
class PileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $piles = Pile::with('servers');

        if ($request->has('all')) {
            $piles = $piles->allTeams();
        }

        return response()->json($piles->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pile = Pile::findOrFail($id);
        $pile->fill([
            'user_id' => \Auth::user()->id,
            'name'    => $request->get('name'),
        ]);

        $pile->save();

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
        return response()->json(Pile::with('servers')->findOrFail($id));
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
     * Changes the users pile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePile(Request $request)
    {
        $user =\Auth::user();

        $user->current_pile_id = $request->get('pile');
        $user->save();

        return response()->json($user->load(['currentTeam', 'currentPile']));
    }
}
