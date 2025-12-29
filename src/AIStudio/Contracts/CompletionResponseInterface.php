<?php

namespace AIStudio\Contracts;

interface CompletionResponseInterface
{
    /**
     * Get the main text from the completion response
     *
     * @return string|null
     */
    public function getText(): ?string;

    /**
     * Get all choices/alternatives from the response
     *
     * @return array
     */
    public function getChoices(): array;

    /**
     * Get token usage information
     *
     * @return array{prompt_tokens: int, completion_tokens: int, total_tokens: int}
     */
    public function getUsage(): array;

    /**
     * Get model identifier/version
     *
     * @return string
     */
    public function getModel(): string;

    /**
     * Convert response to array
     *
     * @return array
     */
    public function toArray(): array;
}
