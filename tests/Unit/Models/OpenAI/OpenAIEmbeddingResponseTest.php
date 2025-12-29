<?php

namespace AIStudio\Tests\Unit\Models\OpenAI;

use AIStudio\Contracts\EmbeddingResponseInterface;
use AIStudio\Models\OpenAI\OpenAIEmbeddingResponse;
use PHPUnit\Framework\TestCase;

class OpenAIEmbeddingResponseTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $response = new OpenAIEmbeddingResponse('list', [], 'model', ['prompt_tokens' => 0, 'total_tokens' => 0]);

        $this->assertInstanceOf(EmbeddingResponseInterface::class, $response);
    }

    public function testOpenAIEmbeddingResponseCreation(): void
    {
        $data = [
            [
                'object' => 'embedding',
                'embedding' => [0.1, 0.2, 0.3, 0.4, 0.5],
                'index' => 0,
            ],
        ];
        $model = 'text-search-doc/latest';
        $usage = ['prompt_tokens' => 8, 'total_tokens' => 8];

        $response = new OpenAIEmbeddingResponse('list', $data, $model, $usage);

        $this->assertEquals('list', $response->object);
        $this->assertEquals($data, $response->data);
        $this->assertEquals($model, $response->model);
        $this->assertEquals($usage, $response->usage);
    }

    public function testFromArray(): void
    {
        $data = [
            'object' => 'list',
            'data' => [
                [
                    'object' => 'embedding',
                    'embedding' => [0.1, 0.2, 0.3],
                    'index' => 0,
                ],
            ],
            'model' => 'text-search-doc/latest',
            'usage' => [
                'prompt_tokens' => 5,
                'total_tokens' => 5,
            ],
        ];

        $response = OpenAIEmbeddingResponse::fromArray($data);

        $this->assertEquals($data['object'], $response->object);
        $this->assertEquals($data['data'], $response->data);
        $this->assertEquals($data['model'], $response->model);
        $this->assertEquals($data['usage'], $response->usage);
    }

    public function testGetEmbeddings(): void
    {
        $data = [
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
        ];

        $response = new OpenAIEmbeddingResponse('list', $data, 'model', ['prompt_tokens' => 10, 'total_tokens' => 10]);

        $embeddings = $response->getEmbeddings();

        $this->assertCount(2, $embeddings);
        $this->assertEquals([0.1, 0.2, 0.3], $embeddings[0]);
        $this->assertEquals([0.4, 0.5, 0.6], $embeddings[1]);
    }

    public function testGetTotalTokens(): void
    {
        $usage = ['prompt_tokens' => 8, 'total_tokens' => 8];
        $response = new OpenAIEmbeddingResponse('list', [], 'model', $usage);

        $this->assertEquals(8, $response->getTotalTokens());
    }

    public function testGetModel(): void
    {
        $response = new OpenAIEmbeddingResponse('list', [], 'text-search-doc/latest', ['prompt_tokens' => 0, 'total_tokens' => 0]);

        $this->assertEquals('text-search-doc/latest', $response->getModel());
    }

    public function testToArray(): void
    {
        $data = [
            [
                'object' => 'embedding',
                'embedding' => [0.1, 0.2, 0.3],
                'index' => 0,
            ],
        ];
        $model = 'text-search-doc/latest';
        $usage = ['prompt_tokens' => 5, 'total_tokens' => 5];

        $response = new OpenAIEmbeddingResponse('list', $data, $model, $usage);

        $array = $response->toArray();

        $this->assertEquals('list', $array['object']);
        $this->assertEquals($data, $array['data']);
        $this->assertEquals($model, $array['model']);
        $this->assertEquals($usage, $array['usage']);
    }
}
