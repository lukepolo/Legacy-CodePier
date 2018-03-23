<?php

namespace App\Services\Site;

use GuzzleHttp\Client;

class AcmeDnsService
{
    public function register()
    {
        $client = new Client();
        try {
            $response = $client->post(config('app.url_dns').'/register');
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            throw new \Exception('DNS Server is down, please contact support');
        }
    }
}
