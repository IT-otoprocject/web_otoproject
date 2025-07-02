<?php

namespace App\Services;

use GuzzleHttp\Client;

class OdooApiService
{
    protected $client;
    protected $url;
    protected $db;
    protected $username;
    protected $password;
    protected $uid;

    public function __construct($config)
    {
        $this->url = $config['url'];
        $this->db = $config['db'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->client = new Client(['verify' => false]);
        $this->uid = $this->authenticate();
    }

    protected function authenticate()
    {
        $response = $this->client->post($this->url . '/jsonrpc', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'common',
                    'method' => 'login',
                    'args' => [$this->db, $this->username, $this->password],
                ],
                'id' => 1,
            ],
        ]);
        $body = json_decode($response->getBody(), true);
        return $body['result'] ?? null;
    }

    public function getProducts($fields = ['name', 'default_code', 'list_price'], $offset = 0, $limit = 50, $domain = [])
    {
        $searchDomain = $domain ?: [];
        $response = $this->client->post($this->url . '/jsonrpc', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'call',
                'params' => [
                    'service' => 'object',
                    'method' => 'execute_kw',
                    'args' => [
                        $this->db,
                        $this->uid,
                        $this->password,
                        'product.product',
                        'search_read',
                        [$searchDomain],
                        ['fields' => $fields, 'offset' => $offset, 'limit' => $limit],
                    ],
                ],
                'id' => 2,
            ],
        ]);
        $body = json_decode($response->getBody(), true);
        return $body['result'] ?? [];
    }
}
