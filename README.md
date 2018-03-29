# GoogleAuth  ![PHP >= 7.1](https://img.shields.io/badge/php-%3E%3D%207.1-lightgrey.svg?colorB=476daa)

## Overview

PHP library to use Google SSO Authentication in Onyx project.

## Getting started

### Requirements : Google Tokens

* Go to https://console.developers.google.com
* Create a new project
* In the new project, go to "library", search for Google+ API and activate it
* In Google+ API, click on "create new identifiers"
* Fill the input "redirection URI"
* Download the json config file
* Put the file in your project


### Installation

Download deps in your project via composer:
```bash
composer require naoned/google-auth
```

Add GoogleAuth to your container:
```php
use Pimple\Container;
use Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

$container['google.auth'] = function(Container $c) {
    return new GoogleAuth('path/to/your/config.json', $c['request_stack'], $c['url_generator']);
};
```

### Usage
```php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;
use Naoned\GoogleAuth\Exceptions\GoogleError;
use Naoned\GoogleAuth\Exceptions\BadRequest;

class Controller
{   
    private
        $auth;

    public function __construct(GoogleAuth $auth)
    {
        $this->auth = $auth;
    }

    // Go to google account choice and connection
    public function loginProcessAction(): Response
    {
        return new RedirectResponse($this->auth->loginUrl());
    }
    
    // Callback call by google once the user is connected
    // this callback url is defined in the generated config.json token
    public function callbackAction(): Response
    {
        try
        {
            // log the user with google api
            $mail = $this->auth->loginProcess();
        }
        catch (GoogleError $e)
        {
            // error throw by google api
            throw new \HttpException(500, $e->getMessage());
        }
        catch (BadRequest $e)
        {
            // error throw if the request hasn't the right parameters
            throw new \HttpException(400, $e->getMessage());
        }

        // Connexion succesfull with mail "$mail";

        // Go where you want once connected
        return $this->redirect('homepage');
    }

    public function logoutAction(): Response
    {
        // redirect to google logout and go to the specified route (ex: login.unconnected)
        return new RedirectResponse($this->auth->logoutUrl('login.unconnected', ['params' => 'my value']));
    }
}
```
