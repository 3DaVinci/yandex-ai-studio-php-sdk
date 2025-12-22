<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Models\TokenizeResponse;
use AIStudio\Exceptions\ApiException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

class Tokenize
{
    private const ENDPOINT = '/foundationModels/v1/tokenize';

    public function __construct(
        private Client $client
    ) {}

    /**
     * Tokenize text using Yandex AI Studio API
     *
     * @param string $modelUri
     * @param string $text
     * @return TokenizeResponse
     * @throws ApiException
     */
    public function create(string $modelUri, string $text): TokenizeResponse
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

            return TokenizeResponse::fromArray($data);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface $e) {
            throw new ApiException('Failed to tokenize text: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (DecodingExceptionInterface $e) {
            throw new ApiException('Failed to decode response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
