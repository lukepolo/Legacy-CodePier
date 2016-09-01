<?php

namespace App\Observers;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\DeploymentStep;
use App\Models\Site;

/**
 * Class SiteObserver.
 */
class SiteObserver
{
    public static $originalServers = [];

    private $siteService;
    private $repositoryService;

    /**
     * SiteObserver constructor.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService                        $siteService
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(SiteService $siteService, RepositoryService $repositoryService)
    {
        $this->siteService = $siteService;
        $this->repositoryService = $repositoryService;
    }

    public function saved(Site $site)
    {
        if (! empty($site->repository)) {
            $this->repositoryService->importSshKeyIfPrivate($site);
        }

//        $this->siteService->updateSiteNginxConfig($site);
//        $this->siteService->renameDomain($site, $request->get('domain'));

        if ($site->deploymentSteps->count() == 0) {
            $defaultSteps = [
                [
                    'step'                         => 'Clone Repository',
                    'order'                        => '1',
                    'internal_deployment_function' => 'cloneRepository',
                    'customizable'                 => false,
                ],
                [
                    'step'                         => 'Install PHP Dependencies',
                    'order'                        => '2',
                    'internal_deployment_function' => 'installPhpDependencies',
                    'customizable'                 => true,
                ],
//                [
//                    'step' => 'Install Node Dependencies',
//                    'order' => '3',
//                    'internal_deployment_function' => 'installNodeDependencies',
//                    'customizable' => true
//                ],
                [
                    'step'                         => 'Run Migrations',
                    'order'                        => '4',
                    'internal_deployment_function' => 'runMigrations',
                    'customizable'                 => true,
                ],
                [
                    'step'                         => 'Setup Release',
                    'order'                        => '5',
                    'internal_deployment_function' => 'setupFolders',
                    'customizable'                 => false,
                ],
                [
                    'step'                         => 'Clean Up Old Releases',
                    'order'                        => '6',
                    'internal_deployment_function' => 'cleanup',
                    'customizable'                 => true,
                ],
            ];

            foreach ($defaultSteps as $defaultStep) {
                DeploymentStep::firstOrCreate(
                    array_merge(['site_id' => $site->id], $defaultStep)
                );
            }
        }
    }
}
