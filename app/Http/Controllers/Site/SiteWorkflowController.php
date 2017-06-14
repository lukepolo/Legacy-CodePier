<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\WorkflowRequest;

class SiteWorkflowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @param WorkflowRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(WorkflowRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'workflow' => $request->get('workflow'),
        ]);

        return response()->json($site);
    }
}
