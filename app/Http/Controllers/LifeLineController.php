<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Site\Lifeline;
use App\Events\Site\LifeLineUpdated;

class LifeLineController extends Controller
{
    /**
     * @param $lifelineHashId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($lifelineHashId)
    {
        $lifeline = Lifeline::findOrFail(\Hashids::decode($lifelineHashId)[0]);

        $lifeline->update([
            'last_seen' => Carbon::now(),
        ]);

        broadcast(new LifeLineUpdated($lifeline));

        return response()->json('OK');
    }
}
