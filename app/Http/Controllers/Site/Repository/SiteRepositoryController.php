<?php

namespace App\Http\Controllers\Site\Repository;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Http\Controllers\Controller;
use App\Models\DeploymentStep;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteRepositoryController extends Controller
{
    private $serverService;
    private $repositoryService;

    /**
     * SiteController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService                         $serverService
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(ServerService $serverService, RepositoryService $repositoryService)
    {
        $this->serverService = $serverService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // TODO - repository should be its own model
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $repository = $request->get('repository');

        $site = Site::with('server')->findOrFail($request->get('site_id'));

        $sshKey = $this->serverService->getFile($site->server, '/home/codepier/.ssh/id_rsa.pub');

        if (empty($sshKey)) {
            return back()->withErrors('You seem to be missing a SSH key, please contact support.');
        }

        $site->fill([
            'repository'                  => $repository,
            'branch'                      => $request->get('branch'),
            'zerotime_deployment'         => $request->get('zerotime_deployment'),
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
        ]);

        $this->repositoryService->importSshKeyIfPrivate($site->userRepositoryProvider, $repository, $sshKey);

        $site->save();

        if ($site->zerotime_deployment) {

            // TODO - move to proper area

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
                [
                    'step'                         => 'Install Node Dependencies',
                    'order'                        => '3',
                    'internal_deployment_function' => 'installNodeDependencies',
                    'customizable'                 => true,
                ],
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
        } else {
            dd('non zerotime deployment');
        }

        return back()->with('success', 'We have added the repository');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $site = Site::with('server')->findOrFail($id);

        $this->serverService->removeFolder($site->server, '/home/codepier/'.$site->domain, 'codepier');
        $this->serverService->createFolder($site->server, '/home/codepier/'.$site->domain, 'codepier');

        foreach ($site->daemons as $daemon) {
            $this->serverService->removeDaemon($site->server, $daemon);
        }

        $site->repository = null;
        $site->branch = null;
        $site->save();

        return back()->with('success', 'deleted repo');
    }
}
