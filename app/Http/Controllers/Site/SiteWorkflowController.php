<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\WorkflowRequest;
use App\Models\Site\Site;

class SiteWorkflowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param WorkflowRequest $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WorkflowRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $flow = collect($request->get('workflow'));

        $tempFlow = $flow->keys()->flip();

        $flow = $flow->map(function ($completed, $workflow) use ($tempFlow) {
            if (is_array($completed)) {
                return $completed;
            }

            if ('message' === $workflow) {
                return $completed;
            }

            return [
                'completed' => $completed,
                'order' => $tempFlow[$workflow] + 1,
            ];
        });

        $site->update([
            'workflow' => $flow,
        ]);

        return response()->json($site);
    }
}
