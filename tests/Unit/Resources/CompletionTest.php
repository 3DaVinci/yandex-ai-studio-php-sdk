<?php

namespace AIStudio\Tests\Unit\Resources;

use AIStudio\Client;
use AIStudio\Resources\Completion;
use AIStudio\Models\CompletionResponse;
use AIStudio\Models\Message;
use AIStudio\Models\CompletionOptions;
use AIStudio\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CompletionTest extends TestCase
{
    public function testCreateCompletionSuccess(): void
    {
        $responseData = [
            'result' => [
                'alternatives' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'text' => 'Hello! How can I help you?',
                        ],
                        'status' => 'ALTERNATIVE_STATUS_FINAL',
                    ],
                ],
                'usage' => [
                    'inputTextTokens' => 10,
                    'completionTokens' => 8,
                    'totalTokens' => 18,
                ],
            ],
            'modelVersion' => '06.12.2023',
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $completion = new Completion($client);

        $messages = [
            new Message('user', 'Hello'),
        ];

        $response = $completion->create(
            'gpt://test-folder/yandexgpt/latest',
            $messages
        );

        $this->assertInstanceOf(CompletionResponse::class, $response);
        $this->assertEquals('Hello! How can I help you?', $response->getText());
        $this->assertEquals($responseData['modelVersion'], $response->getModelVersion());
    }

    public function testCreateCompletionWithOptions(): void
    {
        $responseData = [
            'result' => [
                'alternatives' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'text' => 'Response text',
                        ],
                    ],
                ],
            ],
            'modelVersion' => 'v1',
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $completion = new Completion($client);

        $messages = [new Message('user', 'Test')];
        $options = new CompletionOptions(false, 0.7, 100);

        $response = $completion->create(
            'gpt://test-folder/yandexgpt/latest',
            $messages,
            $options
        );

        $this->assertInstanceOf(CompletionResponse::class, $response);
    }

    public function testCreateCompletionFailure(): void
    {
        $mockResponse = new MockResponse('', ['http_code' => 500]);
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $completion = new Completion($client);

        $messages = [new Message('user', 'Test')];

        $this->expectException(ApiException::class);
        $completion->create('gpt://test-folder/yandexgpt/latest', $messages);
    }
}
