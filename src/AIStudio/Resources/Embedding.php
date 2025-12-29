<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Contracts\EmbeddingResponseInterface;
use AIStudio\Enums\ModelType;
use AIStudio\Models\EmbeddingResponse;
use AIStudio\Models\OpenAI\OpenAIEmbeddingResponse;
use AIStudio\Exceptions\ApiException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class Embedding
{
    private const ENDPOINT_STANDARD = '/foundationModels/v1/textEmbedding';
    private const ENDPOINT_OPENAI = '/embeddings/v1/embeddings';

    public function __construct(
        private Client $client
    ) {}

    /**
     * Get text embeddings using Yandex AI Studio API
     *
     * @param string $modelUri Model URI or identifier
     * @param string|array<string> $text Text or array of texts to embed
     * @param int|null $dimension Embedding dimension (for standard API only)
     * @param ModelType $modelType Type of model API to use
     * @return EmbeddingResponseInterface
     * @throws ApiException
     */
    public function create(
        string $modelUri,
        string|array $text,
        ?int $dimension = null,
        ModelType $modelType = ModelType::STANDARD
    ): EmbeddingResponseInterface {
        try {
            if ($modelType === ModelType::OPENAI_COMPATIBLE) {
                return $this->createOpenAI($modelUri, $text);
            }

            return $this->createStandard($modelUri, is_array($text) ? implode(' ', $text) : $text, $dimension ?? 256);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            throw new ApiException('Failed to get embeddings: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (DecodingExceptionInterface|RedirectionExceptionInterface $e) {
            throw new ApiException('Failed to decode embeddings response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create embeddings using standard Yandex API
     */
    private function createStandard(string $modelUri, string $text, int $dimension): EmbeddingResponse
    {
        $payload = [
            'modelUri' => $modelUri,
            'text' => $text,
            'dim' => $dimension
        ];

        $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT_STANDARD, [
            'json' => $payload,
        ]);

        $data = $response->toArray();

        return EmbeddingResponse::fromArray($data);
    }

    /**
     * Create embeddings using OpenAI-compatible API
     */
    private function createOpenAI(string $model, string|array $input): OpenAIEmbeddingResponse
    {
        $payload = [
            'model' => $model,
            'input' => $input,
        ];

        $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT_OPENAI, [
            'json' => $payload,
        ]);

        $data = $response->toArray();

        return OpenAIEmbeddingResponse::fromArray($data);
    }
}
