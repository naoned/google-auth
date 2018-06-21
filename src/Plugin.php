<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth;

use Onyx\Plugins\AbstractPlugin;
use Onyx\ControllersDeclaration;

class Plugin extends AbstractPlugin
{
    public function getName(): string
    {
        return 'GoogleAuth';
    }

    public function getViewDirectories(): iterable
    {
        return [__DIR__ . '/../views'];
    }

    public function getProviders(): iterable
    {
        return [new GoogleAuthServiceProvider()];
    }

    public function getControllers(): ?ControllersDeclaration
    {
        return new Controllers();
    }
}
