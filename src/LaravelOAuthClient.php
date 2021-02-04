<?php

namespace Werk365\LaravelOAuthClient;

use Exception;
use Illuminate\Support\Facades\Http;

class LaravelOAuthClient
{
    private $vendors;
    private $code;
    private $refresh_token;
    private $vendor;
    private $access_token;
    private $info_body;

    public function __construct($vendor = null)
    {
        $this->vendors = config('laraveloauthclient');
        foreach($this->vendors as $v => $value){
            $lower_v = strtolower($v);
            $this->vendors[$lower_v] = $this->vendors[$v];
        }
        $this->vendor = strtolower($vendor) ?? null;
    }

    public function setVendor($vendor): self
    {
        $this->vendor = strtolower($vendor);

        return $this;
    }

    public function setCode($code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setRedirectUri($redirect_uri, $vendor = null): self
    {
        $vendor = $vendor ?? $this->vendor;
        $this->vendors[$vendor]['redirect_uri'] = $redirect_uri;

        return $this;
    }

    public function setRefreshToken($refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function setAccessToken($access_token): self
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getToken($code = null): array
    {
        $this->code = $code ?? $this->code;
        if (! $this->code) {
            throw new Exception('No code set');
        } elseif (! $this->vendor) {
            throw new Exception('No vendor set');
        }
        $response = $this->tokenHandler('token');

        return $response;
    }

    public function refreshToken($refresh_token = null)
    {
        $this->refresh_token = $refresh_token ?? $this->refresh_token;
        if (! $this->refresh_token) {
            throw new Exception('No refresh token set');
        } elseif (! $this->vendor) {
            throw new Exception('No vendor set');
        }
        $response = $this->tokenHandler('refresh');

        return $response;
    }

    public function getInfo($access_token = null, $body = null)
    {
        $this->info_body = $body ?? $this->info_body ?? null;
        $this->access_token = $access_token ?? $this->access_token;
        if (! $this->access_token) {
            throw new Exception('No access token set');
        } elseif (! $this->vendor) {
            throw new Exception('No vendor set');
        }

        return $this->infoHandler();
    }

    private function tokenHandler(string $type): ?array
    {
        $body = [
            'client_id' => $this->vendors[$this->vendor]['client_id'],
            'client_secret' => $this->vendors[$this->vendor]['client_secret'],
            'redirect_uri' => $this->vendors[$this->vendor]['redirect_uri'],
            'grant_type' => $this->vendors[$this->vendor][$type]['grant_type'],
        ];

        if ($type == 'token') {
            $body['code'] = $this->code;
        } elseif ($type == 'refresh') {
            $body['refresh_token'] = $this->refresh_token;
        }

        $token = [];

        switch ($this->vendors[$this->vendor][$type]['auth']) {
            case 'body':
                if ($this->vendors[$this->vendor][$type]['method'] === 'POST') {
                    $token = Http::asForm()
                        ->post($this->vendors[$this->vendor][$type]['url'], $body)
                        ->json();
                } else {
                    $token = Http::get($this->vendors[$this->vendor][$type]['url'], $body)
                        ->json();
                }
                break;
            case 'basic':
                if ($this->vendors[$this->vendor][$type]['method'] === 'POST') {
                    $token = Http::withBasicAuth($this->vendors[$this->vendor]['client_id'], $this->vendors[$this->vendor]['client_secret'])
                        ->asForm()
                        ->post($this->vendors[$this->vendor][$type]['url'], $body)
                        ->json();
                } else {
                    $token = Http::withBasicAuth($this->vendors[$this->vendor]['client_id'], $this->vendors[$this->vendor]['client_secret'])
                        ->get($this->vendors[$this->vendor][$type]['url'], $body)
                        ->json();
                }
        }

        $fields = $token;

        if (is_array($this->vendors[$this->vendor][$type]['fields'])) {
            foreach ($this->vendors[$this->vendor][$type]['fields'] as $key => $field) {
                if (isset($token[$key])) {
                    $fields[$field] = $token[$key];
                }
            }
        }

        return $fields ?? null;
    }

    private function infoHandler(): ?array
    {
        if ($this->vendors[$this->vendor]['info']['method'] === 'POST') {
            $info = Http::withToken($this->access_token)->asForm()
                ->post($this->vendors[$this->vendor]['info']['url'], $this->info_body)
                ->json();
        } else {
            $info = Http::withToken($this->access_token)
                ->get($this->vendors[$this->vendor]['info']['url'], $this->info_body)
                ->json();
        }
        $fields = [];
        foreach ($this->vendors[$this->vendor]['info']['fields'] as $field) {
            $fields[$field] = $info[$field];
        }

        return $fields ?? null;
    }
}
