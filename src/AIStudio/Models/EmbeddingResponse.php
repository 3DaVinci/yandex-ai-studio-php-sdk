<?php

namespace AIStudio\Models;

use AIStudio\Contracts\EmbeddingResponseInterface;

class EmbeddingResponse implements EmbeddingResponseInterface
{
    public function __construct(
        private array $embedding,
        private int $numTokens,
        private string $modelVersion
    ) {}

    public function getEmbedding(): array
    {
        return $this->embedding;
    }

    public function getEmbeddings(): array
    {
        return $this->embedding;
    }

    public function getNumTokens(): int
    {
        return $this->numTokens;
    }

    public function getTotalTokens(): int
    {
        return $this->numTokens;
    }

    public function getModelVersion(): string
    {
        return $this->modelVersion;
    }

    public function getModel(): string
    {
        return $this->modelVersion;
    }

    public function toArray(): array
    {
        return [
            'embedding' => $this->embedding,
            'numTokens' => $this->numTokens,
            'modelVersion' => $this->modelVersion,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['embedding'] ?? [],
            $data['numTokens'] ?? 0,
            $data['modelVersion'] ?? ''
        );
    }
}
