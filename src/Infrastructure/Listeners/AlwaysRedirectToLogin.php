<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Listeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Naoned\GoogleAuth\Infrastructure\Controllers\GoogleAuthRoutes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Puzzle\Configuration;

class AlwaysRedirectToLogin implements EventSubscriberInterface
{
    private
        $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        if($request->getSession()->has('user'))
        {
            return;
        }

        $route = $request->attributes->get('_route');
        $regexps = [
            sprintf('^%s($|\.)', GoogleAuthRoutes::ROUTE_PREFIX),
        ];

        $regexps = array_merge(
            $regexps,
            $this->configuration->read('controller/passthru_routes', [])
        );

        foreach($regexps as $regexp)
        {
            if(preg_match("~$regexp~", $route))
            {
                return;
            }
        }

        throw new HttpException(Response::HTTP_UNAUTHORIZED);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 0],
        ]];
    }
}
