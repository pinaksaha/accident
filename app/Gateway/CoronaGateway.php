<?php

namespace App\Gateway;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class CoronaGateway
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getConfirmedData()
    {
        $uri = Config::get('covid.confirmed_endpoint');
        return $this->client->get($uri)->getBody()->getContents();
    }
}
