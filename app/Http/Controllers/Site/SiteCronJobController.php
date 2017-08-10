<?php

namespace App\Http\Controllers\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\CronJobRequest;
use App\Events\Site\SiteCronJobCreated;
use App\Events\Site\SiteCronJobDeleted;
use App\Http\Requests\CronJobUpdatedRequest;
use App\Events\Site\FixSiteServerConfigurations;

class SiteCronJobController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->cronJobs
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CronJobRequest $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(CronJobRequest $request, $siteId)
    {
        $site = Site::with('cronJobs')->findOrFail($siteId);

        $job = $request->get('job');
        $user = $request->get('user');

        if (! $site->cronJobs
            ->where('job', $job)
            ->where('user', $user)
            ->count()
        ) {
            $cronJob = CronJob::create([
                'job' => $job,
                'user' => $user,
                'servers' => $request->get('servers', []),
                'server_types' => $request->get('server_types', []),
            ]);

            $site->cronJobs()->save($cronJob);

            broadcast(new SiteCronJobCreated($site, $cronJob))->toOthers();

            return response()->json($cronJob);
        }

        return response()->json('Cron Job Already Exists', 400);
    }

    /**
     * @param CronJobUpdatedRequest $request
     * @param $siteId
     * @param $cronJobId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CronJobUpdatedRequest $request, $siteId, $cronJobId)
    {
        /** @var Site $site */
        $site = Site::with('cronJobs')->findOrFail($siteId);

        $cronJob = CronJob::findOrFail($cronJobId);

        $cronJob->update([
            'servers' => $request->get('servers', []),
            'server_types' => $request->get('server_types', []),
        ]);

        event(new FixSiteServerConfigurations($site));

        return response()->json($cronJob);
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
        $site = Site::with('cronJobs')->findOrFail($siteId);

        event(new SiteCronJobDeleted($site, $site->cronJobs->keyBy('id')->get($id)));

        return response()->json($site->cronJobs()->detach($id));
    }
}
