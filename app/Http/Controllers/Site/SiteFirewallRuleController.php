<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Http\Controllers\Controller;
use App\Http\Requests\FirewallRuleRequest;
use App\Events\Site\SiteFirewallRuleCreated;
use App\Events\Site\SiteFirewallRuleDeleted;

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
     * @param FirewallRuleRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(FirewallRuleRequest $request, $siteId)
    {
        $site = Site::with('firewallRules')->findOrFail($siteId);

        $port = $request->get('port');
        $type = $request->get('type', null);
        $fromIp = $request->get('from_ip', null);

        if (! $site->firewallRules
            ->where('port', $port)
            ->where('from_ip', $fromIp)
            ->where('type', $type)
            ->count()
        ) {
            $firewallRule = FirewallRule::create([
                'port' => $port,
                'type' => $type,
                'from_ip' => $fromIp,
                'description' => $request->get('description'),
            ]);

            $site->firewallRules()->save($firewallRule);

            event(new SiteFirewallRuleCreated($site, $firewallRule));

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
