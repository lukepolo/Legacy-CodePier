<?php

namespace App\Http\Controllers\User;

use GuzzleHttp\Client;
use App\Models\User\User;
use GuzzleHttp\Psr7\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Auth::user()->load(['currentTeam', 'currentPile']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id = null)
    {
        $user = empty($id) ? \Auth::user() : User::findOrFail($id);

        $user->fill([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
        ]);

        if ($request->has('password')) {
            $user->password = \Hash::make($request->get('password'));
        }

        $user->save();

        return response()->json($user->load(['currentTeam', 'currentPile']));
    }

    /**
     * Gets the running commands.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRunningCommands()
    {
        return response()->json(
            \Auth::user()->getRunningCommands()
        );
    }

    /**
     * Gets the running deployments.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRunningDeployments()
    {
        return response()->json(
            \Auth::user()->getRunningDeployments()
        );
    }

    /**
     *  Sends a slack invite.
     */
    public function slackInvite()
    {
        $email = \Auth::user()->email;
        $response = $this->inviteToSlackChannel($email);

        \Auth::user()->update([
            'invited_to_slack' => 1,
        ]);

        if (isset($response->error)) {
            if ($response->error == 'already_invited') {
                return back()->withErrors(['You have already been invited. Please check your email : '.$email]);
            } else {
                return back()->withErrors([$response->error]);
            }
        } else {
            return back()->with('success',
                'We have invited you to our slack channel please look at your email : '.$email);
        }
    }

    /**
     * Invites a user to the slack channel via email.
     * @param $email
     * @return mixed
     */
    private function inviteToSlackChannel($email)
    {
        $client = new Client();

        /** @var Response $response */
        $response = $client->post('https://'.config('services.slack.domain').'.slack.com/api/users.admin.invite?t='.time(), [
            'form_params' => [
                'email'      => $email,
                'token'      => config('services.slack.token'),
                'set_active' => true,
                '_attempts'  => '1',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
