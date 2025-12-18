<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Models\EmbeddingResponse;
use AIStudio\Exceptions\ApiException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class Embedding
{
    private const ENDPOINT = '/foundationModels/v1/textEmbedding';

    public function __construct(
        private Client $client
    ) {}

    /**
     * Get text embeddings using Yandex AI Studio API
     *
     * @param string $modelUri
     * @param string $text
     * @return EmbeddingResponse
     * @throws ApiException
     */
    public function create(string $modelUri, string $text): EmbeddingResponse
    {
        try {
            $payload = [
                'modelUri' => $modelUri,
                'text' => $text,
            ];

            $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT, [
                'json' => $payload,
            ]);

            $data = $response->toArray();

            return EmbeddingResponse::fromArray($data);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            throw new ApiException('Failed to get embeddings: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (DecodingExceptionInterface|RedirectionExceptionInterface $e) {
            throw new ApiException('Failed to decode embeddings response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
