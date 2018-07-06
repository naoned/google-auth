<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth;

use Onyx\ControllersDeclaration;

class Controllers implements ControllersDeclaration
{
    public function getMountPoints(): iterable
    {
        return [
            ['/google_auth', new Infrastructure\Controllers\Provider()],
        ];
    }
}
