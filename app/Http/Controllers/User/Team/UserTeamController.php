<?php

namespace App\Http\Controllers\User\Team;

use App\Http\Controllers\Controller;
use Mpociot\Teamwork\Facades\Teamwork;
use App\Http\Requests\User\UserTeamRequest;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;

class UserTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Auth::user()->load('teams.piles');

        return response()->json(\Auth::user()->teams);
    }

    /**
     * Show the members of the given team.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        return response()->json($team);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserTeamRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserTeamRequest $request)
    {
        $teamModel = config('teamwork.team_model');

        $team = $teamModel::create([
            'name'     => $request->name,
            'owner_id' => \Auth::user()->id,
        ]);

        $user = \Auth::user();

        $user->attachTeam($team);

        $team->piles()->sync($request->get('piles', []));
        $team->save();

        $user->current_pile_id = null;
        $user->save();

        return response()->json($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserTeamRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserTeamRequest $request, $id)
    {
        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);
        $team->name = $request->name;

        $team->piles()->sync($request->get('piles', []));
        $team->save();

        $team->save();

        return response()->json($team);
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
        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);
        if (! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $team->delete();

        $userModel = config('teamwork.user_model');
        $userModel::where('current_team_id', $id)
            ->update(['current_team_id' => null]);

        return response()->json();
    }

    /**
     * Switch to the given team.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function switchTeam($id = null)
    {
        $teamModel = config('teamwork.team_model');

        $team = null;

        if (! empty($id)) {
            $team = $teamModel::findOrFail($id);
        }
        try {
            $user = \Auth::user();
            $user->switchTeam($team);
            $user->current_pile_id = null;
            $user->save();
        } catch (UserNotInTeamException $e) {
            abort(403);
        }

        return response()->json($team);
    }

    /**
     * Accept the given invite.
     *
     * @param $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvite($token)
    {
        $invite = Teamwork::getInviteFromAcceptToken($token);
        if (! $invite) {
            abort(404);
        }

        if (auth()->check()) {
            Teamwork::acceptInvite($invite);

            return redirect()->to('/my/teams');
        } else {
            session(['invite_token' => $token]);

            return redirect()->to('login');
        }
    }
}
