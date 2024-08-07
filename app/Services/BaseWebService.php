<?php

namespace app\services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaseWebService
{
    protected $username;
    protected $password;
    protected $baseurl;

    public function __construct()
    {
        $this->username = config('services.restapi.username');
        $this->password = config('services.restapi.password');
        $this->baseurl  = config('services.restapi.baseurl');
    }
    protected function makeAsyncRequest($endpoint)
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->async()
            ->get($this->baseurl . $endpoint);
    }
    public function handleAsyncResponse($promises)
    {
        return collect($promises)->map(function ($promise) {
            try {
                return $promise->wait();
            }catch (\Exception $exception){
                Log::channel('slack')->error('ASYNC PROMISE WAIT ERROR: ' . $exception->getMessage());
                return null;
            }
        });
    }
}

