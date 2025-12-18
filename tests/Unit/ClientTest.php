<?php

namespace AIStudio\Tests\Unit;

use AIStudio\Client;
use AIStudio\Resources\Embedding;
use AIStudio\Resources\Tokenize;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientInitialization(): void
    {
        $client = new Client('test-api-key', 'test-folder-id');
        
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('test-folder-id', $client->getFolderId());
    }
    
    public function testHttpClientIsCreated(): void
    {
        $client = new Client('test-api-key', 'test-folder-id');

        $httpClient = $client->getHttpClient();

        $this->assertNotNull($httpClient);
    }

    public function testEmbeddingResourceIsCreated(): void
    {
        $client = new Client('test-api-key', 'test-folder-id');

        $embedding = $client->embedding();

        $this->assertInstanceOf(Embedding::class, $embedding);
    }

    public function testTokenizeResourceIsCreated(): void
    {
        $client = new Client('test-api-key', 'test-folder-id');

        $tokenize = $client->tokenize();

        $this->assertInstanceOf(Tokenize::class, $tokenize);
    }
}
