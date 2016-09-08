<?php

namespace App\Http\Controllers\User\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;

/**
 * Class UserTeamMemberController.
 */
class UserTeamMemberController extends Controller
{
    /**
     * Show the members of the given team.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        return response()->json($team->users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $team_id
     * @param int $user_id
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param int $id
     */
    public function destroy($team_id, $user_id)
    {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);
        if (! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $userModel = config('teamwork.user_model');
        $user = $userModel::findOrFail($user_id);
        if ($user->getKey() === auth()->user()->getKey()) {
            abort(403);
        }

        $user->detachTeam($team);

        return response()->json();
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function invite(Request $request)
    {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($request->get('team_id'));

        if (! Teamwork::hasPendingInvite($request->email, $team)) {
            Teamwork::inviteToTeam($request->email, $team, function ($invite) {
                Mail::send('emails.team_invite', ['team' => $invite->team, 'invite' => $invite],
                    function ($m) use ($invite) {
                        $m->to($invite->email)->subject('Invitation to join team '.$invite->team->name);
                    });
                // Send email to user
            });
        } else {
            return response()->json([
                'email' => 'The email address is already invited to the team.',
            ]);
        }

        return response()->json();
    }

    /**
     * Resend an invitation mail.
     *
     * @param $invite_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resendInvite($invite_id)
    {
        $invite = TeamInvite::findOrFail($invite_id);

        Mail::send('emails.team_invite', ['team' => $invite->team, 'invite' => $invite],
            function ($m) use ($invite) {
                $m->to($invite->email)->subject('Invitation to join team '.$invite->team->name);
            });

        return response()->json();
    }
}
