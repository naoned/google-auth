<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Controllers;

use Onyx\Traits;
use Puzzle\Configuration;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Naoned\GoogleAuth\Domain\Exceptions\GoogleError;
use Naoned\GoogleAuth\Domain\Exceptions\BadRequest;
use Naoned\GoogleAuth\Infrastructure\Client;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Controller
{
    use
        Traits\RequestAware,
        Traits\SessionAware,
        Traits\TwigAware,
        Traits\UrlGeneratorAware,
        LoggerAwareTrait;

    private
        $client,
        $session,
        $configuration;

    public function __construct(SessionInterface $session, Client $client, Configuration $configuration)
    {
        $this->logger = new NullLogger();
        $this->session = $session;
        $this->client = $client;
        $this->configuration = $configuration;
    }

    public function displayLoginFormAction(): Response
    {
        return $this->render(GoogleAuthRoutes::LOGIN_TEMPLATE_PATH);
    }

    public function loginProcessAction(): Response
    {
        return new RedirectResponse($this->client->loginUrl());
    }

    public function loginCallbackAction(): Response
    {
        try
        {
            $user = $this->client->loginProcess();
        }
        catch(GoogleError $e)
        {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        catch(BadRequest $e)
        {
            throw new HttpException(Response::BAD_REQUEST, $e->getMessage());
        }

        $mail = $user->mail();
        if (! $this->matchAuthorizedMails($mail))
        {
            return new RedirectResponse($this->client->logoutUrl(GoogleAuthRoutes::UNAUTHORIZED_LOGIN));
        }

        $this->session->set('user', $user);

        return $this->redirect($this->configuration->readRequired('redirect_route_after_successfull_login'));
    }

    private function matchAuthorizedMails(string $mail): bool
    {
        $domains = $this->configuration->read('restrictions/domains', []);
        $mails = $this->configuration->read('restrictions/mails', []);

        if (empty($domains) && empty($mails))
        {
            return true;
        }

        foreach ($mails as $restrictiveMail)
        {
            if ($mail === $restrictiveMail)
            {
                return true;
            }
        }

        foreach ($domains as $domain)
        {
            if (preg_match('/@' . $domain . '$/', $mail))
            {
                return true;
            }
        }

        return false;
    }

    public function logoutAction(): Response
    {
        $this->session->remove('user');

        return new RedirectResponse($this->client->logoutUrl(GoogleAuthRoutes::DISPLAY_LOGIN_FORM));
    }

    public function logoutUnauthorizedLogin(): Response
    {
        $this->addErrorFlash('Vous n\'êtes pas autorisé à vous connecter.');

        return new RedirectResponse($this->client->loginUrl());
    }
}
