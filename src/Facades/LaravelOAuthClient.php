<?php

namespace Werk365\LaravelOAuthClient\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelOAuthClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laraveloauthclient';
    }
}
