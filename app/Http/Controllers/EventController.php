<?php

namespace App\Http\Controllers;

use App\Models\Site\SiteDeployment;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siteDeployments = SiteDeployment::with(['serverDeployments.server', 'serverDeployments.events.step' => function ($query) {
            $query->withTrashed();
        }, 'site.pile', 'site.userRepositoryProvider.repositoryProvider'])->paginate(10);

        return response()->json($siteDeployments);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        // mark as read
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
