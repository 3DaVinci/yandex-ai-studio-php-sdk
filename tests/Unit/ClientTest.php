<?php

namespace AIStudio\Tests\Unit;

use AIStudio\Client;
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
}
