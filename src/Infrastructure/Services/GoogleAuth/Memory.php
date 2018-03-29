<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

use Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

class Memory implements GoogleAuth
{
    private
        $loginUrl,
        $logoutUrl,
        $loginProcessCallable;

    public function __construct(string $loginUrl, string $logoutUrl, callable $loginProcessCallable)
    {
        $this->loginUrl = $loginUrl;
        $this->logoutUrl = $logoutUrl;
        $this->loginProcessCallable = $loginProcessCallable;
    }

    public function loginUrl(): string
    {
        return $this->loginUrl;
    }

    public function logoutUrl(string $route, array $parameters = []): string
    {
        return $this->logoutUrl;
    }

    public function loginProcess(): string
    {
        return call_user_func($this->loginProcessCallable);
    }
}
