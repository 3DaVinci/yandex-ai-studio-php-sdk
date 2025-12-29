<?php

namespace AIStudio\Tests\Unit\Resources;

use AIStudio\Client;
use AIStudio\Contracts\CompletionResponseInterface;
use AIStudio\Enums\ModelType;
use AIStudio\Resources\Completion;
use AIStudio\Models\CompletionResponse;
use AIStudio\Models\OpenAI\OpenAICompletionResponse;
use AIStudio\Models\Message;
use AIStudio\Models\CompletionOptions;
use AIStudio\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CompletionTest extends TestCase
{
    public function testCreateCompletionStandardSuccess(): void
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
            $messages,
            null,
            ModelType::STANDARD
        );

        $this->assertInstanceOf(CompletionResponse::class, $response);
        $this->assertInstanceOf(CompletionResponseInterface::class, $response);
        $this->assertEquals('Hello! How can I help you?', $response->getText());
        $this->assertEquals($responseData['modelVersion'], $response->getModelVersion());
        $this->assertEquals($responseData['modelVersion'], $response->getModel());

        $usage = $response->getUsage();
        $this->assertEquals(10, $usage['prompt_tokens']);
        $this->assertEquals(8, $usage['completion_tokens']);
        $this->assertEquals(18, $usage['total_tokens']);
    }

    public function testCreateCompletionStandardWithOptions(): void
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
                'usage' => [
                    'inputTextTokens' => 5,
                    'completionTokens' => 3,
                    'totalTokens' => 8,
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
        $this->assertInstanceOf(CompletionResponseInterface::class, $response);
    }

    public function testCreateCompletionOpenAISuccess(): void
    {
        $responseData = [
            'id' => 'chatcmpl-123',
            'object' => 'chat.completion',
            'created' => 1677652288,
            'model' => 'yandexgpt/latest',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Hello! How can I help you?',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 8,
                'total_tokens' => 18,
            ],
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $completion = new Completion($client);

        $messages = [
            ['role' => 'user', 'content' => 'Hello'],
        ];

        $response = $completion->create(
            'yandexgpt/latest',
            $messages,
            ['temperature' => 0.7],
            ModelType::OPENAI_COMPATIBLE
        );

        $this->assertInstanceOf(OpenAICompletionResponse::class, $response);
        $this->assertInstanceOf(CompletionResponseInterface::class, $response);
        $this->assertEquals('Hello! How can I help you?', $response->getText());
        $this->assertEquals('yandexgpt/latest', $response->getModel());

        $usage = $response->getUsage();
        $this->assertEquals(10, $usage['prompt_tokens']);
        $this->assertEquals(8, $usage['completion_tokens']);
        $this->assertEquals(18, $usage['total_tokens']);
    }

    public function testCreateCompletionOpenAIWithMessageObjects(): void
    {
        $responseData = [
            'id' => 'chatcmpl-456',
            'object' => 'chat.completion',
            'created' => 1677652288,
            'model' => 'yandexgpt/latest',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Test response',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens' => 5,
                'completion_tokens' => 3,
                'total_tokens' => 8,
            ],
        ];

        $mockResponse = new MockResponse(json_encode($responseData));
        $mockHttpClient = new MockHttpClient($mockResponse);

        $client = new Client('test-api-key', 'test-folder-id', $mockHttpClient);
        $completion = new Completion($client);

        // Test with Message objects
        $messages = [new Message('user', 'Test')];

        $response = $completion->create(
            'yandexgpt/latest',
            $messages,
            [],
            ModelType::OPENAI_COMPATIBLE
        );

        $this->assertInstanceOf(OpenAICompletionResponse::class, $response);
        $this->assertEquals('Test response', $response->getText());
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
