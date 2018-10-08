<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\WorkflowRequest;

class SiteWorkflowController extends Controller
{
    public function store(WorkflowRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $flow = collect($request->get('workflow'));

        $site->update([
            'workflow' => $flow,
        ]);

        return $site;
    }
}
