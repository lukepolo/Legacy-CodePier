<?php

namespace App\Http\Controllers;

use App\Jobs\Site\CreateSite;
use App\Models\Server\Server;

use App\Traits\ServerCommandTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ServerCommandTrait;
}
