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
            'fields' => [
                'access_token' => 'access_token',
                'expires_in' => 'expires_in',
                'refresh_token' => 'refresh_token',
            ],
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
