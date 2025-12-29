<?php

namespace AIStudio\Models\OpenAI;

use AIStudio\Contracts\CompletionResponseInterface;

class OpenAICompletionResponse implements CompletionResponseInterface
{
    /**
     * @param string $id
     * @param string $object
     * @param int $created
     * @param string $model
     * @param array<int, array{index: int, message: array{role: string, content: string}, finish_reason: string}> $choices
     * @param array{prompt_tokens: int, completion_tokens: int, total_tokens: int} $usage
     */
    public function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly int $created,
        public readonly string $model,
        public readonly array $choices,
        public readonly array $usage
    ) {}

    public function getText(): ?string
    {
        return $this->choices[0]['message']['content'] ?? null;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function getUsage(): array
    {
        return $this->usage;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            object: $data['object'] ?? 'chat.completion',
            created: $data['created'] ?? time(),
            model: $data['model'] ?? '',
            choices: $data['choices'] ?? [],
            usage: $data['usage'] ?? ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0]
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'choices' => $this->choices,
            'usage' => $this->usage,
        ];
    }
}
