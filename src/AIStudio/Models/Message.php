<?php

namespace AIStudio\Models;

class Message
{
    public function __construct(
        private string $role,
        private string $text
    ) {}
    
    public function getRole(): string
    {
        return $this->role;
    }
    
    public function getText(): string
    {
        return $this->text;
    }
    
    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'text' => $this->text,
        ];
    }
}
