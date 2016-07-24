<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Jobs\CreateSite;
use App\Jobs\DeploySite;
use App\Models\DeploymentStep;
use App\Models\Server;
use App\Models\Site;
use App\Models\SiteDaemon;
use App\Models\SiteSettings;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    protected $siteService;
    protected $serverService;
    protected $repositoryService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Server\Site\SiteService |SiteService $siteService
     */
    public function __construct(
        RepositoryService $repositoryService,
        ServerService $serverService,
        SiteService $siteService
    ) {
        $this->siteService = $siteService;
        $this->serverService = $serverService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getSite($serverID, $siteID)
    {
        return view('server.site.index', [
            'site' => Site::with('server')->findOrFail($siteID)
        ]);
    }

    /**
     * @return mixed
     */
    public function postCreateSite($serverID)
    {
        $server = Server::findOrFail($serverID);

        $this->dispatch(new CreateSite(
                $server, \Request::get('domain'),
                (int)\Request::get('wildcard_domain'),
                \Request::get('web_directory')
            )
        );
//            ->onQueue('site_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postInstallRepository($serverID, $siteID)
    {
        $repository = \Request::get('repository');

        $site = Site::with('server')->findOrFail($siteID);

        $sshKey = $this->serverService->getFile($site->server, '/home/codepier/.ssh/id_rsa.pub');

        if (empty($sshKey)) {
            return back()->withErrors('You seem to be missing a SSH key, please contact support.');
        }

        $site->fill([
            'repository' => $repository,
            'branch' => \Request::get('branch'),
            'zerotime_deployment' => \Request::get('zerotime_deployment'),
            'user_repository_provider_id' => \Request::get('user_repository_provider_id'),
        ]);

        $this->repositoryService->importSshKeyIfPrivate($site->userRepositoryProvider, $repository, $sshKey);

        $site->save();

        if ($site->zerotime_deployment) {

            // TODO - move to proper area

            $defaultSteps = [
                [
                    'step' => 'Clone Repository',
                    'order' => '1',
                    'internal_deployment_function' => 'cloneRepository',
                    'customizable' => false
                ],
                [
                    'step' => 'Install PHP Dependencies',
                    'order' => '2',
                    'internal_deployment_function' => 'installPhpDependencies',
                    'customizable' => true
                ],
                [
                    'step' => 'Install Node Dependencies',
                    'order' => '3',
                    'internal_deployment_function' => 'installNodeDependencies',
                    'customizable' => true
                ],
                [
                    'step' => 'Run Migrations',
                    'order' => '4',
                    'internal_deployment_function' => 'runMigrations',
                    'customizable' => true
                ],
                [
                    'step' => 'Setup Release',
                    'order' => '5',
                    'internal_deployment_function' => 'setupFolders',
                    'customizable' => false
                ],
                [
                    'step' => 'Clean Up Old Releases',
                    'order' => '6',
                    'internal_deployment_function' => 'cleanup',
                    'customizable' => true
                ],
            ];

            foreach ($defaultSteps as $defaultStep) {
                DeploymentStep::create(
                    array_merge(['site_id' => $site->id], $defaultStep)
                );
            }
        } else {
            dd('non zerotime deployment');
        }

        return back()->with('success', 'We have added the repository');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return $this
     */
    public function postRequestLetsEncryptSSLCert($serverID, $siteID)
    {
        $errors = $this->siteService->installSSL(Site::with('server')->findOrFail($siteID), \Request::get('domains'));

        if (is_array($errors)) {
            return back()->withErrors($errors);
        }

        return back()->with('success', 'You have successfully installed your ssl cert');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRemoveSSL($serverID, $siteID)
    {
        $this->siteService->removeSSL(Site::findOrFail($siteID));

        return back()->with('success', 'You have disabled your SSL certificate');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postRenameDomain($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->renameDomain($site, \Request::get('domain'));

        return back()->with('success', 'Updated name');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getDeploy($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->dispatch(new DeploySite($site));

        return back()->with('success', 'we are currently deploying');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postInstallWorker($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->installDaemon(
            $site,
            '/home/codepier/' . $site->domain . ($site->zerotime_deployment ? '/current' : null) . '/artisan queue:work ' . \Request::get('connection') . ' --timeout=' . \Request::get('timeout') . ' --sleep=' . \Request::get('sleep') . ' --tries=' . \Request::get('tries') . ' ' . (\Request::get('daemon') ? '--daemon' : null),
            true,
            true,
            'codepier',
            1
        );

        return back()->with('success', 'You have added a worker');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @param $workerID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRemoveWorker($serverID, $siteID, $workerID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->removeDaemon($site->server, SiteDaemon::findOrFail($workerID));

        return back()->with('success', 'You have removed the worker');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUpdateWebDirectory($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $site->web_directory = \Request::get('web_directory');
        $site->save();

        $this->siteService->updateSiteNginxConfig($site);

        return back()->with('success', 'You have updated the web directory');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRemoveRepository($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->serverService->removeFolder($site->server, '/home/codepier/' . $site->domain, 'codepier');
        $this->serverService->createFolder($site->server, '/home/codepier/' . $site->domain, 'codepier');

        foreach ($site->daemons as $daemon) {
            $this->serverService->removeDaemon($site->server, $daemon);
        }

        $site->repository = null;
        $site->branch = null;
        $site->save();

        return back()->with('success', 'deleted repo');
    }

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDeleteSite($serverID, $siteID)
    {
        $site = Site::with([
            'server',
            'daemons'
        ])->findOrFail($siteID);

        $this->siteService->deleteSite($site);

        return redirect()->action('ServerController@getServer', $serverID);
    }


    /*
     * TODO - these need to be somehow generated
     */

    /**
     * @param $serverID
     * @param $siteID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSavePHPSettings($serverID, $siteID)
    {
        $site = Site::with('settings')->findOrFail($siteID);

        $siteSettings = SiteSettings::firstOrCreate([
            'site_id' => $siteID
        ]);

        $siteSettings->data = array_merge(
            empty($siteSettings->data) ? [] : $siteSettings->data, [
                'max_upload_size' => \Request::get('max_upload_size')
            ]
        );

        $this->siteService->updateMaxUploadSize($site, \Request::get('max_upload_size'));

        $siteSettings->save();

        return back()->with('success', 'You have updated your PHP settings');
    }
}
