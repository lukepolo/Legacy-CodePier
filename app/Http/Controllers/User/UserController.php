<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User\User;
use App\Jobs\UserDataBundle;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateMarketing;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\UserUpdateDataProcessing;
use Spatie\Newsletter\NewsletterFacade as NewsLetter;

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
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request)
    {
        $user = $request->user();

        $user->fill([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'workflow' => $request->get('workflow', $user->workflow),
            'second_auth_active' => $request->get('second_auth_active', $user->second_auth_active),
        ]);

        if ($request->has('password')) {
            $user->password = \Hash::make($request->get('password'));
        }

        $user->save();

        return response()->json($user->fresh());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestData()
    {
        $user = \Auth::user();
        if ($user->last_bundle_download && $user->last_bundle_download->addDays(2) >= Carbon::now()) {
            return response()->json('Your data is still processing, please wait till its been sent to you.', 500);
        }

        $user->update([
            'last_bundle_download' => Carbon::now()
        ]);

        dispatch(new UserDataBundle(\Auth::user()));
        return response()->json('OK');
    }

    /**
     * @param UserUpdateMarketing $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMarketing(UserUpdateMarketing $request, $id = null)
    {
        $user = empty($id) ? $request->user() : User::findOrFail($id);

        $user->fill([
            'marketing' => $request->get('marketing'),
        ]);

        $user->save();


        if ($user->marketing) {
            Newsletter::subscribeOrUpdate($user->email, [
                'FNAME' => $user->name,
            ]);
        }

        if (! $user->marketing) {
            Newsletter::unsubscribe($user->email);
        }

        return response()->json($user->fresh());
    }

    /**
     * @param UserUpdateDataProcessing $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDataProcessing(UserUpdateDataProcessing $request, $id = null)
    {
        $user = empty($id) ? $request->user() : User::findOrFail($id);

        $user->fill([
            'processing' => $request->get('processing'),
        ]);

        $user->save();

        return response()->json($user->fresh());
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy()
    {
        \Auth::user()->delete();
        return response()->json('OK');
    }
}
