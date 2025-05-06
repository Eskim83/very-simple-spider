<?php

namespace VerySimpleSpider\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Downloader
{
    protected ?Client $client = null;

    public function __construct(?Client $client = null)
    {
        if ($client !== null) {
            $this->client = $client;
        } elseif (class_exists(Client::class)) {
            $this->client = new Client([
                'headers' => ['User-Agent' => 'VerySimpleSpider/2.0'],
                'timeout' => 10,
            ]);
        }
    }

    public function fetch(string $url): ?string
    {
        if ($this->client) {
            try {
                $response = $this->client->get($url);
                return (string) $response->getBody();
            } catch (RequestException $e) {
                return null;
            }
        }

        // Fallback na file_get_contents
        $context = stream_context_create([
            'http' => ['user_agent' => 'VerySimpleSpider/2.0']
        ]);

        return @file_get_contents($url, false, $context) ?: null;
    }
}
