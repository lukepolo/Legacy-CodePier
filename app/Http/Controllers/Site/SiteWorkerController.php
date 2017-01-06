<?php

namespace App\Http\Controllers\Site;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerRequest;
use App\Events\Site\SiteWorkerCreated;
use App\Events\Site\SiteWorkerDeleted;

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
     * @param WorkerRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WorkerRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $worker = Worker::create([
            'auto_start' => $request->get('auto_start'),
            'auto_restart' => $request->get('auto_restart'),
            'user' => $request->get('user'),
            'command' => $request->get('command'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $site->workers()->save($worker);

        event(new SiteWorkerCreated($site, $worker));

        return response()->json($worker);
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
        $site = Site::with('workers')->findOrFail($siteId);

        event(new SiteWorkerDeleted($site, $site->workers->keyBy('id')->get($id)));

        return response()->json('OK');
    }
}
