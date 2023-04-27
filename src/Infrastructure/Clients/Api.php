<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Clients;

use Onyx\Traits\UrlGeneratorAware;
use Onyx\Traits\RequestAware;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Naoned\GoogleAuth\Infrastructure\Client;
use Naoned\GoogleAuth\Domain\Entities\GoogleUser;
use Puzzle\Configuration;
use Naoned\GoogleAuth\Domain\Exceptions\BadRequest;
use Naoned\GoogleAuth\Domain\Exceptions\GoogleError;

class Api implements Client
{
    use
        RequestAware,
        UrlGeneratorAware;

    private
        $oauthService,
        $request,
        $googleLogoutUrl,
        $client;

    private const
        GOOGLE_LOGOUT_URL = 'https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=';

    public function __construct(Configuration $config, RequestStack $request, UrlGeneratorInterface $urlGenerator, array $additionnalScopes)
    {
        $this->client = $this->createClient($config);
        foreach ($additionnalScopes as $scope)
        {
            $this->client->addScope($scope);
        }

        $this->oauthService = new \Google_Service_Oauth2($this->client);
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

    public function loginProcess(): GoogleUser
    {
        if (!$this->request->query->has('code'))
        {
            throw new BadRequest('Code parameter must be provided');
        }

        $code = $this->request->query->get('code');

        $result = $this->client->authenticate($code);
        if (isset($result['error_description']))
        {
            throw new GoogleError($result['error_description']);
        }

        $user = $this->oauthService->userinfo_v2_me->get();

        return new GoogleUser(
            $user->getEmail(),
            $user->getName(),
            $user->getPicture(),
            $this->client->getAccessToken()
        );
    }

    public function client(): \Google_Client
    {
        return $this->client;
    }

    private function createClient(Configuration $config): \Google_Client
    {
        $client = new \Google_Client();
        $client->setAuthConfig($config->readRequired('web'));
        $client->addScope("email");
        $client->setAccessType('offline');

        return $client;
    }
}
