<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Controllers;

use Onyx\Traits;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Naoned\GoogleAuth\Domain\Exceptions\GoogleError;
use Naoned\GoogleAuth\Domain\Exceptions\BadRequest;
use Naoned\GoogleAuth\Infrastructure\Client;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Naoned\GoogleAuth\Domain\WhitelistChecker;

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
        $checker,
        $redirectRoute;

    public function __construct(SessionInterface $session, Client $client, WhitelistChecker $checker, string $redirectRoute)
    {
        $this->logger = new NullLogger();
        $this->session = $session;
        $this->client = $client;
        $this->checker= $checker;
        $this->redirectRoute = $redirectRoute;
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
        if(! $this->checker->allow($mail))
        {
            return new RedirectResponse($this->client->logoutUrl(GoogleAuthRoutes::UNAUTHORIZED_LOGIN));
        }

        $this->session->set('user', $user);

        return $this->redirect($this->redirectRoute);
    }

    public function logoutAction(): Response
    {
        $this->session->remove('user');

        return $this->redirect($this->redirectRoute);
    }

    public function logoutUnauthorizedLogin(): Response
    {
        $this->addErrorFlash('Vous n\'êtes pas autorisé à vous connecter.');

        return $this->logoutAction();
    }
}
