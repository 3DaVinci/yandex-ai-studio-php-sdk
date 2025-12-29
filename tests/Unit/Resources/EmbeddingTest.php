<?php

namespace AIStudio\Tests\Unit\Resources;

use AIStudio\Client;
use AIStudio\Contracts\EmbeddingResponseInterface;
use AIStudio\Enums\ModelType;
use AIStudio\Resources\Embedding;
use AIStudio\Models\EmbeddingResponse;
use AIStudio\Models\OpenAI\OpenAIEmbeddingResponse;
use AIStudio\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class EmbeddingTest extends TestCase
{
    public function testCreateEmbeddingStandardSuccess(): void
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
            'Test text for embedding',
            256,
            ModelType::STANDARD
        );

        $this->assertInstanceOf(EmbeddingResponse::class, $response);
        $this->assertInstanceOf(EmbeddingResponseInterface::class, $response);
        $this->assertEquals($responseData['embedding'], $response->getEmbedding());
        $this->assertEquals($responseData['embedding'], $response->getEmbeddings());
        $this->assertEquals($responseData['numTokens'], $response->getNumTokens());
        $this->assertEquals($responseData['numTokens'], $response->getTotalTokens());
        $this->assertEquals($responseData['modelVersion'], $response->getModelVersion());
        $this->assertEquals($responseData['modelVersion'], $response->getModel());
    }

    public function testCreateEmbeddingOpenAISuccess(): void
    {
        $responseData = [
            'object' => 'list',
            'data' => [
                [
                    'object' => 'embedding',
                    'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
                    'index' => 0,
                ],
            ],
            'model' => 'text-search-doc/latest',
            'usage' => [
                'prompt_tokens' => 8,
                'total_tokens' => 8,
            ],
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $embedding = new Embedding($client);

        $response = $embedding->create(
            'text-search-doc/latest',
            'Test text for embedding',
            null,
            ModelType::OPENAI_COMPATIBLE
        );

        $this->assertInstanceOf(OpenAIEmbeddingResponse::class, $response);
        $this->assertInstanceOf(EmbeddingResponseInterface::class, $response);
        $this->assertEquals([[0.1, 0.2, 0.3, 0.4, 0.5]], $response->getEmbeddings());
        $this->assertEquals(8, $response->getTotalTokens());
        $this->assertEquals('text-search-doc/latest', $response->getModel());
    }

    public function testCreateEmbeddingOpenAIWithMultipleInputs(): void
    {
        $responseData = [
            'object' => 'list',
            'data' => [
                [
                    'object' => 'embedding',
                    'embedding' => [0.1, 0.2, 0.3],
                    'index' => 0,
                ],
                [
                    'object' => 'embedding',
                    'embedding' => [0.4, 0.5, 0.6],
                    'index' => 1,
                ],
            ],
            'model' => 'text-search-doc/latest',
            'usage' => [
                'prompt_tokens' => 16,
                'total_tokens' => 16,
            ],
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $embedding = new Embedding($client);

        $response = $embedding->create(
            'text-search-doc/latest',
            ['Text 1', 'Text 2'],
            null,
            ModelType::OPENAI_COMPATIBLE
        );

        $this->assertInstanceOf(OpenAIEmbeddingResponse::class, $response);
        $this->assertEquals([[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]], $response->getEmbeddings());
        $this->assertEquals(16, $response->getTotalTokens());
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
            'Test text',
            256
        );
    }
}
