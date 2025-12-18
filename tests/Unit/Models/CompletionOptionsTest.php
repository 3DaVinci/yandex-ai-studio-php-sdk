<?php

namespace AIStudio\Tests\Unit\Models;

use AIStudio\Models\CompletionOptions;
use PHPUnit\Framework\TestCase;

class CompletionOptionsTest extends TestCase
{
    public function testDefaultOptions(): void
    {
        $options = new CompletionOptions();
        
        $array = $options->toArray();
        
        $this->assertFalse($array['stream']);
        $this->assertArrayNotHasKey('temperature', $array);
        $this->assertArrayNotHasKey('maxTokens', $array);
    }
    
    public function testCustomOptions(): void
    {
        $options = new CompletionOptions(
            stream: true,
            temperature: 0.7,
            maxTokens: 1000
        );
        
        $array = $options->toArray();
        
        $this->assertTrue($array['stream']);
        $this->assertEquals(0.7, $array['temperature']);
        $this->assertEquals(1000, $array['maxTokens']);
    }
}
