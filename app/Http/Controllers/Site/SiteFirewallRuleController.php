<?php

namespace App\Http\Controllers\Site;

use App\Events\SiteFirewallRuleCreated;
use App\Http\Controllers\Controller;
use App\Models\FirewallRule;
use App\Models\Site\Site;
use App\Http\Requests\Site\SiteFirewallRuleRequest;

class SiteFirewallRuleController extends Controller
{
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
     * @param SiteFirewallRuleRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SiteFirewallRuleRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $firewallRule = FirewallRule::create([
            'port' => $request->get('port'),
            'from_ip' => $request->get('from_ip', null),
            'description' => $request->get('description'),
        ]);

        $site->firewallRules()->save($firewallRule);

        event(new SiteFirewallRuleCreated($site, $firewallRule));

        return response()->json($firewallRule);
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
        $site = Site::findOrFail($siteId);

        event(new SiteFirewallRuleCreated($site, $site->firewallRules->get($id)));

        return response()->json('OK');
    }
}
