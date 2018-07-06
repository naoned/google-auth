<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Onyx\Application;
use Puzzle\PrefixedConfiguration;
use Naoned\GoogleAuth\Infrastructure\Clients\Api;
use Naoned\GoogleAuth\Infrastructure\Listeners\AlwaysRedirectToLogin;
use Naoned\GoogleAuth\Infrastructure\Controllers\GoogleAuthRoutes;
use Naoned\GoogleAuth\Domain\Services\WhitelistCheckers\ArrayBased;

class GoogleAuthServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $container)
    {
        $container['google_auth.configuration'] = function(Container $c) {
            return new PrefixedConfiguration($c['configuration'], 'google_auth');
        };

        $container['google_auth.client'] = function(Container $c) {
            return new Api($c['google_auth.configuration'], $c['request_stack'], $c['url_generator']);
        };

        $container['google_auth.listeners.alwaysRedirectToLogin'] = function(Container $c) {
            return new AlwaysRedirectToLogin();
        };

        $container['google_auth.whitelistChecker'] = function(Container $c) {
            $configuration = new PrefixedConfiguration($c['configuration'], 'google_auth/controller');
            return new ArrayBased(
                $configuration->read('restrictions/domains', []),
                $configuration->read('restrictions/mails', [])
            );
        };
    }

    public function subscribe(Container $container, EventDispatcherInterface $dispatcher): void
    {
        if($container['google_auth.configuration']->read('redirect_all', false) === true)
        {
            $dispatcher->addSubscriber($container['google_auth.listeners.alwaysRedirectToLogin']);
        }
    }

    public static function registerErrorHandler(Application $app): void
    {
        $app->error(function (HttpException $e) use($app) {

            $caughtCodes = [
                Response::HTTP_BAD_REQUEST,
                Response::HTTP_UNAUTHORIZED,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ];

            if(in_array($e->getStatusCode(), $caughtCodes))
            {
                if($e->getStatusCode() !== Response::HTTP_UNAUTHORIZED)
                {
                    $app['session']->getFlashBag()->add('error', $e->getMessage());
                }

                $message = $app['twig']->render(GoogleAuthRoutes::LOGIN_TEMPLATE_PATH);

                return new Response($message, $e->getStatusCode());
            }
        });
    }
}
