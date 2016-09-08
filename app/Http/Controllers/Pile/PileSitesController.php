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
        return response()->json(Pile::with('sites.servers')->findOrFail($pileId)->sites);
    }
}
