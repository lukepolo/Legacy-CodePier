<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\DeploySiteRequest;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Requests\Site\SiteServerFeatureRequest;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteFirewallRule;

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
     * @param SiteRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        $site = Site::create([
            'user_id'             => \Auth::user()->id,
            'domain'              => $request->get('domainless') == true ? 'default' : $request->get('domain'),
            'pile_id'             => $request->get('pile_id'),
            'name'                => $request->get('domain')
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
     * @param SiteRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $id)
    {
        $site = Site::findOrFail($id);

        $site->update([
            'branch'                      => $request->get('branch'),
            'domain'                      => $request->get('domainless') == true ? 'default' : $request->get('domain'),
            'name'                        => $request->get('domain'),
            'pile_id'                     => $request->get('pile_id'),
            'framework'                   => $request->get('framework'),
            'repository'                  => $request->get('repository'),
            'web_directory'               => $request->get('web_directory'),
            'wildcard_domain'             => (int) $request->get('wildcard_domain'),
            'zerotime_deployment'         => true,
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
            'type'                        => $request->get('type')
        ]);

        if ($request->has('servers')) {
            $changes = $site->servers()->sync($request->get('servers', []));

            foreach ($changes['attached'] as $serverID) {
                $this->dispatch(new CreateSite(Server::findOrFail($serverID), $site));
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
     * @param DeploySiteRequest $request
     */
    public function deploy(DeploySiteRequest $request)
    {
        $site = Site::with('servers')->findOrFail($request->get('site'));

        $this->dispatch(new DeploySite($site));
    }

    /**
     * @param SiteServerFeatureRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSiteServerFeatures(SiteServerFeatureRequest $request, $id)
    {
        $site = Site::findOrFail($id);

        return response()->json(
            $site->update([
                'server_features' => $request->get('services'),
            ])
        );
    }
}
