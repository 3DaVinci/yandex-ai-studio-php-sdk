<?php

namespace AIStudio\Models;

class CompletionOptions
{
    public function __construct(
        private bool $stream = false,
        private ?float $temperature = null,
        private ?int $maxTokens = null
    ) {}
    
    public function toArray(): array
    {
        $options = [
            'stream' => $this->stream,
        ];
        
        if ($this->temperature !== null) {
            $options['temperature'] = $this->temperature;
        }
        
        if ($this->maxTokens !== null) {
            $options['maxTokens'] = $this->maxTokens;
        }
        
        return $options;
    }
}
