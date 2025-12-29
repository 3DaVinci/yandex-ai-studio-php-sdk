<?php

namespace AIStudio\Contracts;

interface EmbeddingResponseInterface
{
    /**
     * Get embeddings data
     *
     * @return array<float>|array<int, array<float>>
     */
    public function getEmbeddings(): array;

    /**
     * Get total number of tokens used
     *
     * @return int
     */
    public function getTotalTokens(): int;

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
