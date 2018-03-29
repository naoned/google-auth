<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

use Onyx\Traits\UrlGeneratorAware;
use Onyx\Traits\RequestAware;
use Naoned\GoogleAuth\Exceptions\GoogleError;
use Naoned\GoogleAuth\Exceptions\BadRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

class Api implements GoogleAuth
{
    use
        RequestAware,
        UrlGeneratorAware;

    private
        $plus,
        $request,
        $googleLogoutUrl,
        $client;

    private const
        GOOGLE_LOGOUT_URL = 'https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=';

    public function __construct(string $clientConfig, RequestStack $request, UrlGeneratorInterface $urlGenerator)
    {
        $this->client = $this->createClient($clientConfig);
        $this->plus = new \Google_Service_Plus($this->client);
        $this->setRequest($request);
        $this->setUrlGenerator($urlGenerator);
    }


    public function loginUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function logoutUrl(string $route, array $parameters = []): string
    {
        return self::GOOGLE_LOGOUT_URL . $this->url($route, $parameters);
    }

    public function loginProcess(): string
    {
        if (!$this->request->query->has('code'))
        {
            throw new BadRequest('Code parameter must be provided.');
        }

        $code = $this->request->query->get('code');

        $result = $this->client->authenticate($code);
        if (isset($result['error_description']))
        {
            throw new GoogleError($result['error_description']);
        }

        return $this->plus->people->get("me")['emails'][0]['value'];
    }

    private function createClient(string $configFilePath): \Google_Client
    {
        if (!is_file($configFilePath))
        {
            throw new \Exception(sprintf('File %s does not exist.', $configFilePath));
        }

        $client = new \Google_Client();
        $client->setAuthConfig($configFilePath);
        $client->addScope(\Google_Service_Plus::USERINFO_EMAIL);
        $client->setAccessType('offline');

        return $client;
    }
}
