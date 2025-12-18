<?php

namespace AIStudio;

use AIStudio\Resources\Embedding;
use AIStudio\Resources\Tokenize;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private const string API_BASE_URL = 'https://llm.api.cloud.yandex.net';
    
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $folderId;
    
    public function __construct(string $apiKey, string $folderId, ?HttpClientInterface $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->folderId = $folderId;
        $this->httpClient = $httpClient ?? HttpClient::create([
            'base_uri' => self::API_BASE_URL,
            'headers' => [
                'Authorization' => 'Api-Key ' . $this->apiKey,
                'x-folder-id' => $this->folderId,
            ],
        ]);
    }
    
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }
    
    public function getFolderId(): string
    {
        return $this->folderId;
    }

    public function embedding(): Embedding
    {
        return new Embedding($this);
    }

    public function tokenize(): Tokenize
    {
        return new Tokenize($this);
    }
}
