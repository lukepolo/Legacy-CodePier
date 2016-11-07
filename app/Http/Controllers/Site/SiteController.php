<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Site;
use App\Models\Site\SiteFirewallRule;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Site::get()
        );
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
        $site = Site::create([
            'user_id'             => \Auth::user()->id,
            'domain'              => $request->get('domainless') == true ? 'default' : $request->get('domain'),
            'pile_id'             => $request->get('pile_id'),
            'name'                => $request->get('domain'),
        ]);

        SiteFirewallRule::create([
            'site_id'   => $site->id,
            'description' => 'HTTP',
            'port'        => '80',
            'from_ip'     => null,
        ]);

        SiteFirewallRule::create([
            'site_id'   => $site->id,
            'description' => 'HTTPS',
            'port'        => '443',
            'from_ip'     => null,
        ]);


        $defaultSteps = [
            [
                'step'                         => 'Clone Repository',
                'order'                        => '1',
                'internal_deployment_function' => 'cloneRepository',
                'customizable'                 => false,
            ],
            [
                'step'                         => 'Install PHP Dependencies',
                'order'                        => '2',
                'internal_deployment_function' => 'installPhpDependencies',
                'customizable'                 => true,
            ],
            [
                'step' => 'Install Node Dependencies',
                'order' => '3',
                'internal_deployment_function' => 'installNodeDependencies',
                'customizable' => true,
            ],
            [
                'step'                         => 'Run Migrations',
                'order'                        => '4',
                'internal_deployment_function' => 'runMigrations',
                'customizable'                 => true,
            ],
            [
                'step'                         => 'Setup Release',
                'order'                        => '5',
                'internal_deployment_function' => 'setupFolders',
                'customizable'                 => false,
            ],
            [
                'step'                         => 'Clean Up Old Releases',
                'order'                        => '6',
                'internal_deployment_function' => 'cleanup',
                'customizable'                 => true,
            ],
        ];

        foreach ($defaultSteps as $defaultStep) {
            DeploymentStep::create(
                array_merge(['site_id' => $site->id], $defaultStep)
            );
        }

        return response()->json($site);
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
        return response(
            Site::with('servers')->findOrFail($id)
        );
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
        $site = Site::findOrFail($id);

        $site->update([
            'branch'                      => $request->get('branch'),
            'domain'                      => $request->get('domain'),
            'pile_id'                     => $request->get('pile_id'),
            'framework'                   => $request->get('framework'),
            'repository'                  => $request->get('repository'),
            'web_directory'               => $request->get('web_directory'),
            'wildcard_domain'             => (int) $request->get('wildcard_domain'),
            'zerotime_deployment'         => true,
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
        ]);

        if ($request->has('servers')) {
            $changes = $site->servers()->sync($request->get('servers', []));

            foreach ($changes['attached'] as $serverID) {
                $this->dispatchNow(new CreateSite(Server::findOrFail($serverID), $site));
            }

            foreach ($changes['detached'] as $serverID) {
                dd('site needs to be deleted');
            }
        }

        return response()->json($site);
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
        return response()->json(
            Site::findOrFail($id)->delete()
        );
    }

    /**
     * Deploys a site.
     *
     * @param Request $request
     */
    public function deploy(Request $request)
    {
        $site = Site::with('servers')->findOrFail($request->get('site'));

        $this->dispatch(new DeploySite($site));
    }

    public function updateSiteServerFeatures(Request $request, $id)
    {
        $site = Site::findOrFail($id);

        return response()->json(
            $site->update([
                'server_features' => $request->get('services'),
            ])
        );
    }
}
