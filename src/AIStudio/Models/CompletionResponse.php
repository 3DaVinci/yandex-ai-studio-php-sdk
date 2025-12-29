<?php

namespace AIStudio\Models;

use AIStudio\Contracts\CompletionResponseInterface;

class CompletionResponse implements CompletionResponseInterface
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

    public function getModel(): string
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

    public function getChoices(): array
    {
        return $this->result['alternatives'] ?? [];
    }

    public function getUsage(): array
    {
        $usage = $this->result['usage'] ?? [];

        // Normalize to match interface format
        return [
            'prompt_tokens' => $usage['inputTextTokens'] ?? 0,
            'completion_tokens' => $usage['completionTokens'] ?? 0,
            'total_tokens' => $usage['totalTokens'] ?? 0,
        ];
    }

    public function toArray(): array
    {
        return [
            'result' => $this->result,
            'modelVersion' => $this->modelVersion,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['result'] ?? [],
            $data['modelVersion'] ?? ''
        );
    }
}
