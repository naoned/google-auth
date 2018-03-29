<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Services;

interface GoogleAuth
{
    public function loginUrl(): string;

    public function logoutUrl(string $route, array $parameters = []): string;

    public function loginProcess(): string;
}
