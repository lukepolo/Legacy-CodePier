<?php

namespace App\Http\Controllers;

use App\Models\BuoyApp;
use App\Http\Requests\BuoyRequest;
use App\Contracts\BuoyServiceContract as BuoyService;

class BuoyAppController extends Controller
{
    /**
     * @var BuoyService
     */
    private $buoyService;

    /**
     * BuoyAppController constructor.
     * @param \App\Services\Buoys\BuoyService | BuoyService $buoyService
     */
    public function __construct(BuoyService $buoyService)
    {
        $this->middleware('role:admin', [
            'only' => [
                'update',
                'destroy'
            ]
        ]);

        $this->buoyService = $buoyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(BuoyApp::paginate(25));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(BuoyApp::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BuoyRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BuoyRequest $request, $id)
    {
        $buoy = BuoyApp::findOrFail($id);

        $buoy->fill([
            'title' => $request->get('title'),
            'buoy_class' => $request->get('buoy_class'),
            'description' => $request->get('description'),
            'ports' => json_decode($request->get('ports')),
            'options' => json_decode($request->get('options')),
        ]);

        if($request->hasFile('icon')) {
            $buoy->icon = $request->file('icon')->store('buoy_icons', 'public');
        }

        $buoy->save();

        return response()->json($buoy);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(BuoyApp::destroy($id));
    }

    /**
     * Gets the buoy classes that are in our repository.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBuoyClasses()
    {
        return response()->json($this->buoyService->getBuoyClasses());
    }
}
