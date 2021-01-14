# Laravel OAuth Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI][ico-styleci]][link-styleci]

Multi-purpose OAuth client, configurable for a sleuth of providers through the config file.

## Installation

Via Composer

``` bash
$ composer require werk365/laraveloauthclient
```

## Usage

Publish the config
```bash
$ php artisan vendor:publish --provider="Werk365\LaravelOAuthClient\LaravelOAuthClientServiceProvider"
```
Configure the config using the provided example.

Then use the package as follows:
```php
use Werk365\LaravelOAuthClient\LaravelOAuthClient as OAuth;

// ...

$oauth = new OAuth("vendorname");

// Returns array defined in config
$response = $oauth->getToken($code);

//Returns array defined in config
$response = $oauth->getInfo($accesstoken);

//Returns array defined in config
$response = $oauth->refreshToken($refreshtoken);
```

## Config
Example of config provided, edit values to match vendor spec:
```php
<?php

return [
    'vendor1' => [
        'client_id' => '1234',
        'client_secret' => '12345',
        'redirect_uri' => 'https://www.example.com/oauth2/vendor1',
        'token' => [
            'url' => 'https://login.vendor.example.com/oauth2/token',
            'method' => 'POST',
            'grant_type' => 'authorization_code',
            'fields' => [
                'access_token' => 'access_token',
                'expires_in' => 'expires_in',
                'refresh_token' => 'refresh_token',
            ],
            'auth' => 'body',
        ],
        'refresh' => [
            'url' => 'https://login.vendor.example.com/oauth2/token',
            'method' => 'POST',
            'grant_type' => 'authorization_code',
            'fields' => "*",
            'auth' => 'body',
        ],
        'info' => [
            'url' => 'https://login.vendor.example.com/oauth2/metadata',
            'method' => 'GET',
            'fields' => [
                'metadata1',
                'metadata2',
            ],
        ],
    ],
    'vendor2' => ['...'],
];

```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

[ico-version]: https://img.shields.io/packagist/v/werk365/laraveloauthclient.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/werk365/laraveloauthclient.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/werk365/laraveloauthclient/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/328643005/shield

[link-packagist]: https://packagist.org/packages/werk365/laraveloauthclient
[link-downloads]: https://packagist.org/packages/werk365/laraveloauthclient
[link-styleci]: https://styleci.io/repos/328643005
[link-author]: https://github.com/365Werk
[link-contributors]: ../../contributors
