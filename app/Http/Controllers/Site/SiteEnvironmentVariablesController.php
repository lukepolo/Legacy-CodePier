<?php

namespace App\Http\Controllers;

use App\Events\Site\SiteEnvironmentVariableCreated;
use App\Events\Site\SiteEnvironmentVariableDeleted;
use App\Models\EnvironmentVariable;
use App\Models\Site\Site;
use Illuminate\Http\Request;

class SiteEnvironmentVariablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::with('environmentVariables')->findOrFail($siteId)->environmentVariables
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::with('environmentVariables')->findOrFail($siteId);

        $variable = $request->get('variable');

        if (! $site->environmentVariables
            ->where('variable', $variable)
            ->count()
        ) {
            $environmentVariable = EnvironmentVariable::create([
                'variable' => $variable,
                'value' => $request->get('value')
            ]);

            $site->environmentVariables()->save($environmentVariable);

            event(new SiteEnvironmentVariableCreated($site, $environmentVariable));

            return response()->json($environmentVariable);
        }

        return response()->json('SSH Key Already Exists', 400);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $siteId)
    {
        $site = Site::with('environmentVariables')->findOrFail($siteId);

        event(new SiteEnvironmentVariableDeleted($site, $site->environmentVariables->keyBy('id')->get($id)));

        return response()->json($site->environmentVariables()->detach($id));
    }
}
