<?php

namespace App\Services\Buoys;

use App\Contracts\Buoys\BuoyContract;
use App\Contracts\BuoyServiceContract;
use App\Traits\SystemFiles;

class BuoyService implements BuoyServiceContract
{
    use SystemFiles;

    /**
     * Gets buoy classes along with there parameters and descriptions
     */
    public function getBuoyClasses()
    {
        $buoys = [];
        foreach($this->getBuoyFiles() as $buoyFile) {
            $buoyReflection = $this->buildReflection($buoyFile);

            if($buoyReflection->implementsInterface(BuoyContract::class)) {

                $installMethod = $buoyReflection->getMethod('install');

                if($this->getFirstDocParam($installMethod, 'buoy-enabled') == true) {
                    $buoyClass = $buoyReflection->getName();
                    $buoys[$buoyClass] = [
                        'title' => $this->getFirstDocParam($installMethod, 'buoy-title')
                    ];

                    foreach($this->getDocParam($installMethod, 'buoy-ports') as $port) {
                        $portParts = explode(':', $port);
                        $buoys[$buoyClass]['ports'][$portParts[0]] = [
                            'local_port' => $portParts[1],
                            'docker_port' => $portParts[2],
                        ];
                    }

                    foreach($this->getDocParam($installMethod, 'buoy-options') as $option) {
                        $optionParts = explode(':', $option);

                        $buoys[$buoyClass]['options'][$optionParts[0]] = [
                            'default' => $optionParts[1],
                            'description' => $this->getFirstDocParam($installMethod, 'buoy-option-desc-'.$optionParts[0])
                        ];
                    }
                }
            }
        }
      
        return collect($buoys);
    }
}