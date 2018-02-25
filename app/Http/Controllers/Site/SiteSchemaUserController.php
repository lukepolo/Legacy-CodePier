<?php

namespace App\Http\Controllers\Site;

use App\Events\Site\SiteSchemaUserCreated;
use App\Events\Site\SiteSchemaUserDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchemaUserRequest;
use App\Models\SchemaUser;
use App\Models\Site\Site;

class SiteSchemaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->schemaUsers
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SchemaUserRequest $request
     * @param int               $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SchemaUserRequest $request, $siteId)
    {
        $site = Site::with('schemaUsers')->findOrFail($siteId);

        $name = $request->get('name');

        if (! $site->schemaUsers
            ->where('name', $name)
            ->count()
        ) {
            $schemaUser = SchemaUser::create([
                'name' => $name,
                'password' => $request->get('password'),
                'schema_ids' =>  $request->get('schema_ids'),
            ]);

            $site->schemaUsers()->save($schemaUser);

            event(new SiteSchemaUserCreated($site, $schemaUser));

            return response()->json($schemaUser);
        }

        return response()->json('Schema user already Exists', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        $site = Site::with('schemas')->findOrFail($siteId);

        event(new SiteSchemaUserDeleted($site, $site->schemaUsers->keyBy('id')->get($id)));

        return response()->json($site->schemaUsers()->detach($id));
    }
}
