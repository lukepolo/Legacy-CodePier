<?php

namespace App\Services\Buoys;

use App\Models\Buoy;
use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Contracts\Buoys\BuoyContract;
use App\Contracts\BuoyServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class BuoyService implements BuoyServiceContract
{
    use SystemFiles;

    private $serverService;
    private $remoteTaskService;

    /**
     * BuoyService constructor.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Gets buoy classes along with there parameters and descriptions.
     */
    public function getBuoyClasses()
    {
        $buoys = [];
        foreach ($this->getBuoyFiles() as $buoyFile) {
            $buoyReflection = $this->buildReflection($buoyFile);

            if ($buoyReflection->implementsInterface(BuoyContract::class)) {
                $installMethod = $buoyReflection->getMethod('install');

                if ($this->getFirstDocParam($installMethod, 'buoy-enabled') == true) {
                    $buoyClass = $buoyReflection->getName();
                    $buoys[$buoyClass] = [
                        'title' => $this->getFirstDocParam($installMethod, 'buoy-title'),
                        'category' => $this->getFirstDocParam($installMethod, 'category'),
                        'description' => $this->getFirstDocParam($installMethod, 'description'),
                    ];

                    foreach ($this->getDocParam($installMethod, 'buoy-ports') as $port) {
                        $portParts = explode(':', $port);
                        $buoys[$buoyClass]['ports'][$portParts[0]] = [
                            'local_port' => $portParts[1],
                            'docker_port' => $portParts[2],
                        ];
                    }

                    foreach ($this->getDocParam($installMethod, 'buoy-options') as $option) {
                        $optionParts = explode(':', $option);

                        $buoys[$buoyClass]['options'][$optionParts[0]] = [
                            'value' => $optionParts[1],
                            'description' => $this->getFirstDocParam($installMethod, 'buoy-option-desc-'.$optionParts[0]),
                        ];
                    }
                }
            }
        }

        return collect($buoys);
    }

    /**
     * Installs a buoy on a server.
     * @param Server $server
     * @param Buoy $buoy
     * @return mixed
     */
    public function installBuoy(Server $server, Buoy $buoy)
    {
        $buoyClass = $buoy->buoyApp->buoy_class;

        return (new $buoyClass($this->serverService, $this->remoteTaskService, $server))->install($buoy->ports, $buoy->options);
    }
}
