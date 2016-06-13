<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\CreateServer;
use App\Jobs\CreateSite;
use App\Models\Server;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    public function postCreateSite()
    {
        $this->dispatch((new CreateSite(\Request::get('domain')))->onQueue('site_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }
}
