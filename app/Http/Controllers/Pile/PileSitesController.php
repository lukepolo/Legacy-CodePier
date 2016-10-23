<?php

namespace App\Http\Controllers\Pile;

use App\Http\Controllers\Controller;
use App\Models\Pile;

/**
 * Class PileSitesController.
 */
class PileSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $pileId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($pileId)
    {
        $pile = Pile::with('sites.servers')->find($pileId);
        $sites = [];
        if ($pile) {
            $sites = $pile->sites;
        }

        return response()->json($sites);
    }
}
