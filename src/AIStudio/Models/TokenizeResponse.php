<?php

namespace AIStudio\Models;

class TokenizeResponse
{
    public function __construct(
        private array $tokens,
        private string $modelVersion
    ) {}

    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function getModelVersion(): string
    {
        return $this->modelVersion;
    }

    public function getTokenCount(): int
    {
        return count($this->tokens);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tokens'] ?? [],
            $data['modelVersion'] ?? ''
        );
    }
}
