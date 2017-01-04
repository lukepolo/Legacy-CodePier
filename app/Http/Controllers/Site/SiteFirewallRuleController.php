<?php

namespace App\Http\Controllers\Site;

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

        return response()->json($firewallRule);
    }

    /**
     * Display the specified resource.
     *
     * @param int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(
            Site::findOrFail($siteId)->get($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteFirewallRuleRequest $request
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteFirewallRuleRequest $request, $siteId, $id)
    {
        $siteFirewallRule = Site::findOrFail($siteId)->get($id);

        return response()->json(
            $siteFirewallRule->update([
                'port' => $request->get('port'),
                'from_ip' => $request->get('from_ip', null),
                'description' => $request->get('description'),
            ])
        );
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
        return response()->json(
            SiteFirewallRule::where('site_id', $siteId)->findOrFail($id)->delete()
        );
    }
}
