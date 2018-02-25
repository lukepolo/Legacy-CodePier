<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Contracts\Repositories\FileRepositoryContract;
use App\Contracts\Server\ServerServiceContract as ServerService;

class FileRepository implements FileRepositoryContract
{
    private $serverService;

    /**
     * FileRepository constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * @param Site | Server $model
     * @param $filePath
     * @param $custom
     *
     * @return File;
     */
    public function findOrCreateFile($model, $filePath, $custom = false, $framework = false)
    {
        $file = $model->files
            ->where('file_path', $filePath)
            ->first();

        if (empty($file) && $model instanceof Site) {
            $file = $this->findOnSiteServers($model, $filePath);
        }

        if (empty($file)) {
            $file = $this->create($filePath, $custom, $framework);
            $model->files()->save($file);
        }

        return $file;
    }

    /**
     * @param $model
     * @param $fileId
     *
     * @return mixed
     */
    public function findOnModelById($model, $fileId)
    {
        return $model->files->keyBy('id')->get($fileId);
    }

    /**
     * @param $filePath
     * @param bool $custom
     * @param bool $framework
     *
     * @return mixed
     */
    private function create($filePath, $custom = false, $framework = false)
    {
        return File::create([
            'custom' => $custom,
            'file_path' => $filePath,
            'framework_file' => $framework,
        ]);
    }

    /**
     * @param File $file
     * @param $content
     *
     * @return File
     */
    public function update(File $file, $content)
    {
        $file->update([
            'content' => $content,
        ]);

        return $file;
    }

    /**
     * @param File $file
     *
     * @return bool
     */
    public function destroy(File $file)
    {
        return $file->delete();
    }

    /**
     * @param File $file
     * @param $server
     *
     * @return File
     */
    public function reload(File $file, $server)
    {
        $file->update([
            'content' => $this->serverService->getFile($server, $file->file_path),
        ]);

        return $file;
    }

    /**
     * @param Site $site
     * @param $fileName
     */
    public function findOnSiteServers(Site $site, $fileName)
    {
        $site->load('provisionedServers.files');

        foreach ($site->provisionedServers as $server) {
            $file = $server->files->first(function ($file) use ($fileName) {
                return $file->file_path == $fileName;
            });
            if (! empty($file)) {
                break;
            }
        }
    }
}
