<?php

namespace App\Services\DeploymentServices\Ruby;

use App\Services\DeploymentServices\DeployTrait;
use App\Services\DeploymentServices\Ruby\Frameworks\RubyOnRails;

class Ruby
{
    use RubyOnRails;
    use DeployTrait;
}
