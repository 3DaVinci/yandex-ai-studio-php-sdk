<?php

namespace AIStudio\Models;

class CompletionResponse
{
    public function __construct(
        private array $result,
        private string $modelVersion
    ) {}

    public function getResult(): array
    {
        return $this->result;
    }

    public function getModelVersion(): string
    {
        return $this->modelVersion;
    }

    public function getText(): ?string
    {
        return $this->result['alternatives'][0]['message']['text'] ?? null;
    }

    public function getAlternatives(): array
    {
        return $this->result['alternatives'] ?? [];
    }

    public function getUsage(): array
    {
        return $this->result['usage'] ?? [];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['result'] ?? [],
            $data['modelVersion'] ?? ''
        );
    }
}
