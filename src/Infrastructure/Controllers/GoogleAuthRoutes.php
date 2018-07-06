<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Controllers;

interface GoogleAuthRoutes
{
    public const
        ROUTE_PREFIX = 'google_auth';

    public const
        DISPLAY_LOGIN_FORM = self::ROUTE_PREFIX . '.display_login_form',
        LOGIN_PROCESS      = self::ROUTE_PREFIX . '.login_process',
        LOGIN_CALLBACK     = self::ROUTE_PREFIX . '.login_callback',
        LOGOUT             = self::ROUTE_PREFIX . '.logout',
        UNAUTHORIZED_LOGIN = self::ROUTE_PREFIX . '.unauthorized_login';

    public const
        LOGIN_TEMPLATE_PATH = 'google_auth/login.twig';
}
