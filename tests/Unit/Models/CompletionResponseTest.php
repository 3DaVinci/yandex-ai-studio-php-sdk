<?php

namespace AIStudio\Tests\Unit\Models;

use AIStudio\Contracts\CompletionResponseInterface;
use AIStudio\Models\CompletionResponse;
use PHPUnit\Framework\TestCase;

class CompletionResponseTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $response = new CompletionResponse([], 'v1');

        $this->assertInstanceOf(CompletionResponseInterface::class, $response);
    }
    public function testCompletionResponseCreation(): void
    {
        $result = [
            'alternatives' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'text' => 'Test response',
                    ],
                    'status' => 'ALTERNATIVE_STATUS_FINAL',
                ],
            ],
            'usage' => [
                'inputTextTokens' => 10,
                'completionTokens' => 5,
                'totalTokens' => 15,
            ],
        ];
        $modelVersion = '06.12.2023';

        $response = new CompletionResponse($result, $modelVersion);

        $this->assertEquals($result, $response->getResult());
        $this->assertEquals($modelVersion, $response->getModelVersion());
        $this->assertEquals('Test response', $response->getText());
    }

    public function testCompletionResponseFromArray(): void
    {
        $data = [
            'result' => [
                'alternatives' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'text' => 'Hello',
                        ],
                    ],
                ],
                'usage' => [
                    'inputTextTokens' => 5,
                    'completionTokens' => 2,
                    'totalTokens' => 7,
                ],
            ],
            'modelVersion' => 'v1',
        ];

        $response = CompletionResponse::fromArray($data);

        $this->assertEquals($data['result'], $response->getResult());
        $this->assertEquals($data['modelVersion'], $response->getModelVersion());
        $this->assertEquals('Hello', $response->getText());
    }

    public function testGetAlternatives(): void
    {
        $alternatives = [
            [
                'message' => ['role' => 'assistant', 'text' => 'Response 1'],
                'status' => 'ALTERNATIVE_STATUS_FINAL',
            ],
        ];

        $response = new CompletionResponse(['alternatives' => $alternatives], 'v1');

        $this->assertEquals($alternatives, $response->getAlternatives());
    }

    public function testGetUsage(): void
    {
        $usage = [
            'inputTextTokens' => 10,
            'completionTokens' => 5,
            'totalTokens' => 15,
        ];

        $response = new CompletionResponse(['usage' => $usage], 'v1');

        $normalized = $response->getUsage();
        $this->assertEquals(10, $normalized['prompt_tokens']);
        $this->assertEquals(5, $normalized['completion_tokens']);
        $this->assertEquals(15, $normalized['total_tokens']);
    }

    public function testGetTextReturnsNullWhenNoAlternatives(): void
    {
        $response = new CompletionResponse([], 'v1');

        $this->assertNull($response->getText());
    }
}
