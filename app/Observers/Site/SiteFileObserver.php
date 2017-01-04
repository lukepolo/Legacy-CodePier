<?php

namespace App\Observers\Site;

use App\Models\File;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\UpdateServerFile;

class SiteFileObserver
{
    use ModelCommandTrait;

    public function created(File $model)
    {
        //        dd('created');
//        foreach ($siteFile->site->provisionedServers as $server) {
//            dispatch(
//                (new UpdateServerFile($server, $siteFile))->onQueue(env('SERVER_COMMAND_QUEUE'))
//            );
//        }
    }

    public function attaching(File $model)
    {
        dd($model);
    }

    public function updated(File $model)
    {
        //        foreach ($siteFile->site->provisionedServers as $server) {
//            dispatch(
//                (new UpdateServerFile($server, $siteFile))->onQueue(env('SERVER_COMMAND_QUEUE'))
//            );
//          }
    }
}
