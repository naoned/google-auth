<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure;

use Naoned\GoogleAuth\Domain\Entities\GoogleUser;

interface Client
{
    public function loginUrl(): string;

    public function logoutUrl(string $route, array $parameters = []): string;

    public function loginProcess(): GoogleUser;
}
