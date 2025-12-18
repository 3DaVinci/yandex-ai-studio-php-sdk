<?php

namespace AIStudio\Tests\Unit\Resources;

use AIStudio\Client;
use AIStudio\Resources\Embedding;
use AIStudio\Models\EmbeddingResponse;
use AIStudio\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class EmbeddingTest extends TestCase
{
    public function testCreateEmbeddingSuccess(): void
    {
        $responseData = [
            'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
            'numTokens' => 10,
            'modelVersion' => '06.12.2023',
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $embedding = new Embedding($client);

        $response = $embedding->create(
            'emb://test-folder/text-search-doc/latest',
            'Test text for embedding'
        );

        $this->assertInstanceOf(EmbeddingResponse::class, $response);
        $this->assertEquals($responseData['embedding'], $response->getEmbedding());
        $this->assertEquals($responseData['numTokens'], $response->getNumTokens());
        $this->assertEquals($responseData['modelVersion'], $response->getModelVersion());
    }

    public function testCreateEmbeddingFailure(): void
    {
        $mockResponse = new MockResponse('', ['http_code' => 500]);
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $embedding = new Embedding($client);

        $this->expectException(ApiException::class);
        $embedding->create(
            'emb://test-folder/text-search-doc/latest',
            'Test text'
        );
    }
}
