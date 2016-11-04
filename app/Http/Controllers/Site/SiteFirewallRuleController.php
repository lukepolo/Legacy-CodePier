<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteFirewallRule;
use Illuminate\Http\Request;

/**
 * Class SiteFirewallRuleController.
 */
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
        return response()->json(SiteFirewallRule::where('site_id', $siteId)->get());
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
        return response()->json(
            SiteFirewallRule::create([
                'site_id' => $siteId,
                'port' => $request->get('port'),
                'from_ip' => $request->get('from_ip', null),
                'description' => $request->get('description'),
            ])
        );
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
        return response()->json(SiteFirewallRule::where('site_id', $siteId)->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteId, $id)
    {
        $siteFirewallRule = SiteFirewallRule::where('site_id', $siteId)->findOrFail($id);

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
        return response()->json(SiteFirewallRule::where('site_id', $siteId)->findOrFail($id)->delete());
    }
}
