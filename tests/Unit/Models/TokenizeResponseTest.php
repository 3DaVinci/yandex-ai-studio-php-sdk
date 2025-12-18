<?php

namespace AIStudio\Tests\Unit\Models;

use AIStudio\Models\TokenizeResponse;
use PHPUnit\Framework\TestCase;

class TokenizeResponseTest extends TestCase
{
    public function testTokenizeResponseCreation(): void
    {
        $tokens = [
            ['id' => 1, 'text' => 'Hello'],
            ['id' => 2, 'text' => ' '],
            ['id' => 3, 'text' => 'world'],
        ];
        $modelVersion = '06.12.2023';

        $response = new TokenizeResponse($tokens, $modelVersion);

        $this->assertEquals($tokens, $response->getTokens());
        $this->assertEquals($modelVersion, $response->getModelVersion());
        $this->assertEquals(3, $response->getTokenCount());
    }

    public function testTokenizeResponseFromArray(): void
    {
        $data = [
            'tokens' => [
                ['id' => 1, 'text' => 'Test'],
            ],
            'modelVersion' => 'v1',
        ];

        $response = TokenizeResponse::fromArray($data);

        $this->assertEquals($data['tokens'], $response->getTokens());
        $this->assertEquals($data['modelVersion'], $response->getModelVersion());
        $this->assertEquals(1, $response->getTokenCount());
    }

    public function testTokenizeResponseFromArrayWithMissingFields(): void
    {
        $data = [];

        $response = TokenizeResponse::fromArray($data);

        $this->assertEquals([], $response->getTokens());
        $this->assertEquals('', $response->getModelVersion());
        $this->assertEquals(0, $response->getTokenCount());
    }
}
