<?php

namespace AIStudio\Models\OpenAI;

use AIStudio\Contracts\EmbeddingResponseInterface;

class OpenAIEmbeddingResponse implements EmbeddingResponseInterface
{
    /**
     * @param string $object
     * @param array<int, array{object: string, embedding: array<float>, index: int}> $data
     * @param string $model
     * @param array{prompt_tokens: int, total_tokens: int} $usage
     */
    public function __construct(
        public readonly string $object,
        public readonly array $data,
        public readonly string $model,
        public readonly array $usage
    ) {}

    public function getEmbeddings(): array
    {
        // Return array of embeddings from all data items
        return array_map(fn($item) => $item['embedding'] ?? [], $this->data);
    }

    public function getTotalTokens(): int
    {
        return $this->usage['total_tokens'] ?? 0;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            object: $data['object'] ?? 'list',
            data: $data['data'] ?? [],
            model: $data['model'] ?? '',
            usage: $data['usage'] ?? ['prompt_tokens' => 0, 'total_tokens' => 0]
        );
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'data' => $this->data,
            'model' => $this->model,
            'usage' => $this->usage,
        ];
    }
}
