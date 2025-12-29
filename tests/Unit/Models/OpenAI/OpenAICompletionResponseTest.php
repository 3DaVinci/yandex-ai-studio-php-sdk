<?php

namespace AIStudio\Tests\Unit\Models\OpenAI;

use AIStudio\Contracts\CompletionResponseInterface;
use AIStudio\Models\OpenAI\OpenAICompletionResponse;
use PHPUnit\Framework\TestCase;

class OpenAICompletionResponseTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $response = new OpenAICompletionResponse('id', 'object', 0, 'model', [], ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]);

        $this->assertInstanceOf(CompletionResponseInterface::class, $response);
    }

    public function testOpenAICompletionResponseCreation(): void
    {
        $id = 'chatcmpl-123';
        $object = 'chat.completion';
        $created = 1677652288;
        $model = 'yandexgpt/latest';
        $choices = [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Hello!',
                ],
                'finish_reason' => 'stop',
            ],
        ];
        $usage = ['prompt_tokens' => 5, 'completion_tokens' => 2, 'total_tokens' => 7];

        $response = new OpenAICompletionResponse($id, $object, $created, $model, $choices, $usage);

        $this->assertEquals($id, $response->id);
        $this->assertEquals($object, $response->object);
        $this->assertEquals($created, $response->created);
        $this->assertEquals($model, $response->model);
        $this->assertEquals($choices, $response->choices);
        $this->assertEquals($usage, $response->usage);
    }

    public function testFromArray(): void
    {
        $data = [
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
                'prompt_tokens' => 10,
                'completion_tokens' => 5,
                'total_tokens' => 15,
            ],
        ];

        $response = OpenAICompletionResponse::fromArray($data);

        $this->assertEquals($data['id'], $response->id);
        $this->assertEquals($data['object'], $response->object);
        $this->assertEquals($data['created'], $response->created);
        $this->assertEquals($data['model'], $response->model);
        $this->assertEquals($data['choices'], $response->choices);
        $this->assertEquals($data['usage'], $response->usage);
    }

    public function testGetText(): void
    {
        $choices = [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Hello! How can I help you?',
                ],
                'finish_reason' => 'stop',
            ],
        ];

        $response = new OpenAICompletionResponse(
            'id',
            'chat.completion',
            1677652288,
            'yandexgpt/latest',
            $choices,
            ['prompt_tokens' => 5, 'completion_tokens' => 8, 'total_tokens' => 13]
        );

        $this->assertEquals('Hello! How can I help you?', $response->getText());
    }

    public function testGetTextReturnsNullWhenNoChoices(): void
    {
        $response = new OpenAICompletionResponse(
            'id',
            'chat.completion',
            1677652288,
            'model',
            [],
            ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]
        );

        $this->assertNull($response->getText());
    }

    public function testGetChoices(): void
    {
        $choices = [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Response 1',
                ],
                'finish_reason' => 'stop',
            ],
        ];

        $response = new OpenAICompletionResponse(
            'id',
            'chat.completion',
            1677652288,
            'model',
            $choices,
            ['prompt_tokens' => 5, 'completion_tokens' => 3, 'total_tokens' => 8]
        );

        $this->assertEquals($choices, $response->getChoices());
    }

    public function testGetUsage(): void
    {
        $usage = [
            'prompt_tokens' => 10,
            'completion_tokens' => 8,
            'total_tokens' => 18,
        ];

        $response = new OpenAICompletionResponse(
            'id',
            'chat.completion',
            1677652288,
            'model',
            [],
            $usage
        );

        $this->assertEquals($usage, $response->getUsage());
    }

    public function testGetModel(): void
    {
        $response = new OpenAICompletionResponse(
            'id',
            'chat.completion',
            1677652288,
            'yandexgpt/latest',
            [],
            ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]
        );

        $this->assertEquals('yandexgpt/latest', $response->getModel());
    }

    public function testToArray(): void
    {
        $id = 'chatcmpl-789';
        $object = 'chat.completion';
        $created = 1677652288;
        $model = 'yandexgpt/latest';
        $choices = [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Test',
                ],
                'finish_reason' => 'stop',
            ],
        ];
        $usage = ['prompt_tokens' => 3, 'completion_tokens' => 1, 'total_tokens' => 4];

        $response = new OpenAICompletionResponse($id, $object, $created, $model, $choices, $usage);

        $array = $response->toArray();

        $this->assertEquals($id, $array['id']);
        $this->assertEquals($object, $array['object']);
        $this->assertEquals($created, $array['created']);
        $this->assertEquals($model, $array['model']);
        $this->assertEquals($choices, $array['choices']);
        $this->assertEquals($usage, $array['usage']);
    }
}
