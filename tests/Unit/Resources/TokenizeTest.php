<?php

namespace AIStudio\Tests\Unit\Resources;

use AIStudio\Client;
use AIStudio\Resources\Tokenize;
use AIStudio\Models\TokenizeResponse;
use AIStudio\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class TokenizeTest extends TestCase
{
    public function testCreateTokenizeSuccess(): void
    {
        $responseData = [
            'tokens' => [
                ['id' => 1, 'text' => 'Hello'],
                ['id' => 2, 'text' => ' '],
                ['id' => 3, 'text' => 'world'],
            ],
            'modelVersion' => '06.12.2023',
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $tokenize = new Tokenize($client);

        $response = $tokenize->create(
            'gpt://test-folder/yandexgpt/latest',
            'Hello world'
        );

        $this->assertInstanceOf(TokenizeResponse::class, $response);
        $this->assertEquals($responseData['tokens'], $response->getTokens());
        $this->assertEquals($responseData['modelVersion'], $response->getModelVersion());
        $this->assertEquals(3, $response->getTokenCount());
    }

    public function testCreateTokenizeFailure(): void
    {
        $mockResponse = new MockResponse('', ['http_code' => 500]);
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $tokenize = new Tokenize($client);

        $this->expectException(ApiException::class);
        $tokenize->create(
            'gpt://test-folder/yandexgpt/latest',
            'Test text'
        );
    }
}
