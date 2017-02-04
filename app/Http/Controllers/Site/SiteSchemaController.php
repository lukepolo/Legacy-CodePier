<?php

namespace App\Http\Controllers\Site;

use App\Models\Schema;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchemaRequest;
use App\Events\Site\SiteSchemaCreated;
use App\Events\Site\SiteSchemaDeleted;

class SiteSchemaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->schemas
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SchemaRequest $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SchemaRequest $request, $siteId)
    {
        $site = Site::with('schemas')->findOrFail($siteId);

        $name = $request->get('name');
        $database = $request->get('database');

        if (! $site->schemas
            ->where('name', $name)
            ->where('database', $database)
            ->count()
        ) {
            $schema = Schema::create([
                'name' => $name,
                'database' =>  $database,
            ]);

            $site->schemas()->save($schema);

            event(new SiteSchemaCreated($site, $schema));

            return response()->json($schema);
        }

        return response()->json('Schema already Exists', 400);
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
        $site = Site::with('schemas')->findOrFail($siteId);

        event(new SiteSchemaDeleted($site, $site->schemas->keyBy('id')->get($id)));

        return response()->json($site->schemas()->detach($id));
    }
}
