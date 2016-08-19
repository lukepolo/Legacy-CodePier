<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Jobs\DeploySite;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Class SiteController
 * @package App\Http\Controllers\Site
 */
class SiteController extends Controller
{
    private $siteService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Site::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $site = Site::create([
            'pile_id' => $request->get('pile_id'),
            'domain' => $request->get('domain'),
            'web_directory' => $request->get('web_directory'),
            'wildcard_domain' => (int)$request->get('wildcard_domain'),
            'zerotime_deployment' => $request->get('zerotime_deployment', true)
        ]);

        $site->servers()->sync($request->get('servers', []));

        return response()->json($site);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(Site::with('servers')->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $site = Site::findOrFail($id);

        $site->fill([
            'branch' => $request->get('branch'),
            'domain' =>  $request->get('domain'),
            'pile_id' => $request->get('pile_id'),
            'repository' => $request->get('repository'),
            'web_directory' => $request->get('web_directory'),
            'wildcard_domain' => (int)$request->get('wildcard_domain'),
            'zerotime_deployment' => $request->get('zerotime_deployment'),
            'user_repository_provider_id' => $request->get('user_repository_provider_id')
        ]);

        if($request->has('servers')) {
            $site->servers()->sync($request->get('servers', []));
        }

        $site->save();

        return response()->json($site);

        // TODO things need to be dispatched to the servers
        $this->siteService->updateSiteNginxConfig($site);
        $this->siteService->renameDomain($site, $request->get('domain'));

        return back()->with('success', 'Updated name');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Site::findOrFail($id)->delete();

        // TODO - dispatch deletling of site
    }

    /**
     * Deploys a site
     *
     * @param Request $request
     */
    public function deploy(Request $request)
    {
        $site = Site::with('server')->findOrFail($request->get('site_id'));

        $this->dispatch(new DeploySite($site));
    }
}


//dd('need to dynamically generate this type of stuff');
//// we can specify the file, and what to search for in the UI?
//$site = Site::with('settings')->findOrFail($id);
//
//$siteSettings = SiteSettings::firstOrCreate([
//    'site_id' => $siteID
//]);
//
//$siteSettings->data = array_merge(
//    empty($siteSettings->data) ? [] : $siteSettings->data, [
//        'max_upload_size' => $request->get('max_upload_size')
//    ]
//);
//
//$this->siteService->updateMaxUploadSize($site, $request->get('max_upload_size'));
//
//$siteSettings->save();
