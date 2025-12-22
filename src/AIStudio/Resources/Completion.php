<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Models\CompletionOptions;
use AIStudio\Models\CompletionResponse;
use AIStudio\Models\Message;
use AIStudio\Exceptions\ApiException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Completion
{
    private const ENDPOINT = '/foundationModels/v1/completion';
    
    public function __construct(
        private Client $client
    ) {}

    /**
     * @param string $modelUri
     * @param Message[] $messages
     * @param CompletionOptions|null $options
     * @return CompletionResponse
     * @throws ApiException
     */
    public function create(
        string $modelUri,
        array $messages,
        ?CompletionOptions $options = null
    ): CompletionResponse {
        try {
            $options = $options ?? new CompletionOptions();

            $payload = [
                'modelUri' => $modelUri,
                'completionOptions' => $options->toArray(),
                'messages' => array_map(fn($msg) => $msg->toArray(), $messages),
            ];

            $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT, [
                'json' => $payload,
            ]);

            $data = $response->toArray();

            return CompletionResponse::fromArray($data);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface $e) {
            throw new ApiException('Failed to create completion: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (DecodingExceptionInterface $e) {
            throw new ApiException('Failed to decode response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
