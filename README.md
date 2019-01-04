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
composer update naoned/google-auth
```

Copy configuration file to your configuration directory
```bash
cp vendor/naoned/google-auth/config/google_auth.yml-dist.example config/built-in/google_auth.yml-dist
cp vendor/naoned/google-auth/config/google_auth.conf.example env/google_auth.conf
```

Then replace variables in `env/google_auth.conf` with yours, and hydrate with `karma`.

Enable GoogleAuth plugin (`config/built-in/plugins.yml`):
```yml
enabled:
  - Naoned\GoogleAuth\Plugin
```
### Possibilities

If you want to require the connection to access your application, add this line to your application global provider:
```php
GoogleAuthServiceProvider::registerErrorHandler($this);
```

If you want to load Api with additionnal scopes (for example to authoize drive access), add this to your provider:
```php
$container['google_auth.additionnalScopes'] = function(Container $c) {
   return [\Google_Service_Drive::DRIVE]; // you can replace or add other scopes
};
```

Overcharge login template by creating `views/google_auth/login.twig` (to make a branded login page)
