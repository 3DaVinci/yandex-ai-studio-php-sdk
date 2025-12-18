<?php

namespace AIStudio\Tests\Unit\Models;

use AIStudio\Models\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testMessageCreation(): void
    {
        $message = new Message('user', 'Hello, AI!');
        
        $this->assertEquals('user', $message->getRole());
        $this->assertEquals('Hello, AI!', $message->getText());
    }
    
    public function testMessageToArray(): void
    {
        $message = new Message('assistant', 'Hello, human!');
        
        $array = $message->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals('assistant', $array['role']);
        $this->assertEquals('Hello, human!', $array['text']);
    }
}
