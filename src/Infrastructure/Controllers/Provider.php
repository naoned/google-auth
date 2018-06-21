<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class Provider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['controller.google_auth'] = function() use($app) {
            $controller = new Controller($app['session'], $app['google_auth.client']);
            $controller
                ->setRequest($app['request_stack'])
                ->setTwig($app['twig'])
                ->setUrlGenerator($app['url_generator'])
            ;

            return $controller;
        };

        $controllers = $app['controllers_factory'];

        $controllers
            ->match('/login/process', 'controller.google_auth:loginProcessAction')
            ->method('GET')
            ->bind(GoogleAuthRoutes::LOGIN_PROCESS);

        $controllers
            ->match('/login', 'controller.google_auth:displayLoginFormAction')
            ->method('GET')
            ->bind(GoogleAuthRoutes::DISPLAY_LOGIN_FORM);

        $controllers
            ->match('/logout', 'controller.google_auth:logoutAction')
            ->method('GET')
            ->bind(GoogleAuthRoutes::LOGOUT);

        $controllers
            ->match('/callback', 'controller.google_auth:loginCallbackAction')
            ->method('GET')
            ->bind(GoogleAuthRoutes::LOGIN_CALLBACK);

        return $controllers;
    }
}
