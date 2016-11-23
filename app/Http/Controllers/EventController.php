<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Site\SiteDeployment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    const COMMANDS = 'commands';
    const SITE_DEPLOYMENTS = 'site_deployments';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $types = $request->get('types', []);
        $piles = $request->get('piles');
        $sites = $request->get('sites');
        $servers = $request->get('servers');

        $queryTypes = [
            'site_deployments' => '(SELECT site_deployments.id, site_deployments.created_at, "'.self::SITE_DEPLOYMENTS.'" as type FROM site_deployments)',
            'commands' => '(SELECT commands.id, commands.created_at, "'.self::COMMANDS.'" as type FROM commands)'
        ];

        $queries = [];

        if(empty($types)) {
            $queries = $queryTypes;
        } else {
            dd('we need to add them there');
        }

        $statement = null;

        foreach($queries as $type => $query) {
            $statement .= ' UNION '.$query;
        }

        $statement .= ' ORDER BY created_at DESC LIMIT 10';

        $topResults = collect(DB::select(ltrim($statement, ' UNION ')));

        return response()->json([

            'site_deployments' => SiteDeployment::with(['serverDeployments.server', 'serverDeployments.events.step' => function ($query) {
                    $query->withTrashed();
                }, 'site.pile', 'site.userRepositoryProvider.repositoryProvider'])->whereIn('id', $topResults->filter(function($event) {
                    return $event->type ==  self::SITE_DEPLOYMENTS;
                })->keyBy('id')->keys())->get(),

            'commands' => Command::whereIn('id', $topResults->filter(function($event) {
                    return $event->type ==  self::COMMANDS;
                })->keyBy('id')->keys())->get()
        ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        // mark as read
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
        //
    }
}
