<?php

namespace App\Http\Controllers;

use App\Models\Bitt;
use App\Jobs\Server\RunBitt;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Requests\BittRequest;

class BittsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return Bitt::paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BittRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BittRequest $request)
    {
        $bitt = Bitt::create([
            'user_id' => \Auth::user()->id,
            'user' => $request->get('user'),
            'title' => $request->get('title'),
            'script' => $request->get('script'),
            'private' => $request->get('private', true),
            'description' => $request->get('description'),
        ]);

//        $bitt->systems()->sync($request->get('systems'));
//        $bitt->categories()->sync((array) $request->get('category'));

//        $bitt->fresh(['systems', 'categories']);

        return response()->json($bitt);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Bitt::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BittRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(BittRequest $request, $id)
    {
        $bitt = Bitt::findOrFail($id);

        $bitt->update([
            'user' => $request->get('user'),
            'title' => $request->get('title'),
            'script' => $request->get('script'),
            'private' => $request->get('private', true),
            'description' => $request->get('description'),
        ]);

//        $bitt->systems()->sync($request->get('systems'));
//        $bitt->categories()->sync((array) $request->get('category'));

        $bitt->fresh(['systems', 'categories']);

        return response()->json($bitt);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        return response()->json(Bitt::findOrFail($id)->delete());
    }

    /**
     * @param Request $request
     * @param $bitt
     * @return \Illuminate\Http\JsonResponse
     */
    public function run(Request $request, $bitt)
    {
        $bitt = Bitt::findOrFail($bitt);

        foreach ($request->get('servers') as $server) {
            dispatch(
                (new RunBitt(Server::findOrFail($server), $bitt))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }

        return response()->json('OK');
    }
}
