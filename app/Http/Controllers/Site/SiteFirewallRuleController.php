<?php

namespace App\Http\Controllers\Site;


use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Http\Controllers\Controller;
use App\Http\Requests\FirewallRuleRequest;
use App\Events\Site\SiteFirewallRuleCreated;
use App\Events\Site\SiteFirewallRuleDeleted;
use App\Contracts\Site\SiteServiceContract as SiteService;

class SiteFirewallRuleController extends Controller
{
    private $siteService;

    /**
     * SiteFirewallRuleController constructor.
     * @param  \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->firewallRules
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FirewallRuleRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(FirewallRuleRequest $request, $siteId)
    {
        if(!empty($firewallRule = $this->siteService->createFirewallRule(
            Site::with('firewallRules')->findOrFail($siteId),
            $request->get('port'),
            $request->get('type'),
            $request->get('description'),
            $request->get('from_ip', null)
        ))) {
            return response()->json($firewallRule);
        }

        return response()->json('Firewall Rule Already Exists', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        $site = Site::with('firewallRules')->findOrFail($siteId);

        event(new SiteFirewallRuleDeleted($site, $site->firewallRules->keyBy('id')->get($id)));

        return response()->json($site->firewallRules()->detach($id));
    }
}
