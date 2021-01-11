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
        $this->vendor = $vendor??null;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function getToken($code = null):array
    {
        $this->code = $code??$this->code;
        if(!$this->code){
            throw new Exception('No code set');
        } else if (!$this->vendor){
            throw new Exception('No vendor set');
        }
        $response = $this->tokenHandler("token");
        return $response;
    }

    public function refreshToken($refresh_token = null)
    {
        $this->refresh_token = $refresh_token??$this->refresh_token;
        if(!$this->refresh_token){
            throw new Exception('No refresh token set');
        } else if (!$this->vendor){
            throw new Exception('No vendor set');
        }
        $response = $this->tokenHandler("refresh");
        return $response;
    }

    public function getInfo($access_token = null, $body = null)
    {
        $this->info_body = $body??$this->info_body??null;
        $this->access_token = $access_token??$this->access_token;
        if(!$this->access_token){
            throw new Exception('No access token set');
        } else if (!$this->vendor){
            throw new Exception('No vendor set');
        }
        return $this->infoHandler();
    }

    private function tokenHandler(string $type): ?array
    {
        $body = [
            "client_id" => $this->vendors[$this->vendor]["client_id"],
            "client_secret" => $this->vendors[$this->vendor]["client_secret"],
            "redirect_uri" => $this->vendors[$this->vendor]["redirect_uri"],
            "grant_type" => "authorization_code"
        ];

        if ($type == "token") {
            $body["code"] = $this->code;
        } else if ($type == "refresh") {
            $body["refresh_token"] = $this->refresh_token;
        }

        switch ($this->vendors[$this->vendor][$type]["auth"]) {
            case "body":
                if ($this->vendors[$this->vendor][$type]["method"] === "POST") {
                    $token = Http::asForm()
                        ->post($this->vendors[$this->vendor][$type]["url"], $body)
                        ->json();
                } else {
                    $token = Http::get($this->vendors[$this->vendor][$type]["url"], $body)
                        ->json();
                }
                break;
            case "basic":
                if ($this->vendors[$this->vendor][$type]["method"] === "POST") {
                    $token = Http::withBasicAuth($this->vendors[$this->vendor]["client_id"], $this->vendors[$this->vendor]["client_secret"])
                        ->asForm()
                        ->post($this->vendors[$this->vendor][$type]["url"], $body)
                        ->json();
                } else {
                    $token = Http::withBasicAuth($this->vendors[$this->vendor]["client_id"], $this->vendors[$this->vendor]["client_secret"])
                        ->get($this->vendors[$this->vendor][$type]["url"], $body)
                        ->json();
                }
        }
        $fields = [];

        foreach ($this->vendors[$this->vendor][$type]["fields"] as $key => $field) {
            $fields[$field] = $token[$field];
        }
        return $fields ?? null;
    }

    private function infoHandler(): ?array
    {
        if ($this->vendors[$this->vendor]["info"]["method"] === "POST") {
            $info = Http::withToken($this->access_token)->asForm()
                ->post($this->vendors[$this->vendor]["info"]["url"], $this->info_body)
                ->json();
        } else {
            $info = Http::withToken($this->access_token)
                ->get($this->vendors[$this->vendor]["info"]["url"], $this->info_body)
                ->json();
        }
        $fields = [];
        foreach ($this->vendors[$this->vendor]["info"]["fields"] as $field) {
            $fields[$field] = $info[$field];
        }
        return $fields ?? null;
    }
}
