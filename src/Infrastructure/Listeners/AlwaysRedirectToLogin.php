<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Listeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Naoned\GoogleAuth\Infrastructure\Controllers\GoogleAuthRoutes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class AlwaysRedirectToLogin implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $regexp = sprintf('/^%s($|.)/', GoogleAuthRoutes::ROUTE_PREFIX);

        if(! preg_match($regexp, $route) && ! $request->getSession()->has('user'))
        {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 0],
        ]];
    }
}
