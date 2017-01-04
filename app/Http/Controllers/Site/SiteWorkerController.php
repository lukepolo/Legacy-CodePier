<?php

namespace App\Http\Controllers\Site;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteWorkerRequest;

class SiteWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->workers
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteWorkerRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteWorkerRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $worker = Worker::create([
            'auto_start' => true,
            'auto_restart' => true,
            'user' => $request->get('user'),
            'command' => $request->get('command'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $site->workers()->save($worker);

        return response()->json($worker);
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(
            Site::findOrFail($siteId)->get($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(
            Site::findOrFail($siteId)->get($id)->delete()
        );
    }
}
