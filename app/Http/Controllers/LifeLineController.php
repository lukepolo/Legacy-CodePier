<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Site\Lifeline;

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
            'sent_notifications' => 0,
            'last_seen' => Carbon::now(),
        ]);

        return response()->json('OK');
    }
}
