<?php

namespace App\Http\Controllers\Pile;

use App\Http\Controllers\Controller;
use App\Models\Pile;
use Illuminate\Http\Request;

/**
 * Class PileController
 *
 * @package App\Http\Controllers\Server
 */
class PileSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Pile::with('sites')->findOrFail($id)->sites);
    }
}
