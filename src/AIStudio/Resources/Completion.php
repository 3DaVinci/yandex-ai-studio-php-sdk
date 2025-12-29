<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Contracts\CompletionResponseInterface;
use AIStudio\Enums\ModelType;
use AIStudio\Models\CompletionOptions;
use AIStudio\Models\CompletionResponse;
use AIStudio\Models\Message;
use AIStudio\Models\OpenAI\OpenAICompletionResponse;
use AIStudio\Exceptions\ApiException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Completion
{
    private const ENDPOINT_STANDARD = '/foundationModels/v1/completion';
    private const ENDPOINT_OPENAI = '/llm/v1alpha/chat/completions';
    
    public function __construct(
        private Client $client
    ) {}

    /**
     * @param string $modelUri Model URI or identifier
     * @param Message[]|array<int, array{role: string, content: string}> $messages
     * @param CompletionOptions|array<string, mixed>|null $options
     * @param ModelType $modelType Type of model API to use
     * @return CompletionResponseInterface
     * @throws ApiException
     */
    public function create(
        string $modelUri,
        array $messages,
        CompletionOptions|array|null $options = null,
        ModelType $modelType = ModelType::STANDARD
    ): CompletionResponseInterface {
        try {
            if ($modelType === ModelType::OPENAI_COMPATIBLE) {
                // Convert Message objects to arrays if needed
                $messagesArray = array_map(
                    fn($msg) => $msg instanceof Message ? $msg->toArray() : $msg,
                    $messages
                );
                return $this->createOpenAI($modelUri, $messagesArray, is_array($options) ? $options : []);
            }

            return $this->createStandard(
                $modelUri,
                $messages,
                $options instanceof CompletionOptions ? $options : ($options ?? new CompletionOptions())
            );
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface $e) {
            throw new ApiException('Failed to create completion: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (DecodingExceptionInterface $e) {
            throw new ApiException('Failed to decode response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create completion using standard Yandex API
     */
    private function createStandard(
        string $modelUri,
        array $messages,
        CompletionOptions $options
    ): CompletionResponse {
        $payload = [
            'modelUri' => $modelUri,
            'completionOptions' => $options->toArray(),
            'messages' => array_map(fn($msg) => $msg instanceof Message ? $msg->toArray() : $msg, $messages),
        ];

        $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT_STANDARD, [
            'json' => $payload,
        ]);

        $data = $response->toArray();

        return CompletionResponse::fromArray($data);
    }

    /**
     * Create completion using OpenAI-compatible API
     */
    private function createOpenAI(
        string $model,
        array $messages,
        array $options = []
    ): OpenAICompletionResponse {
        $payload = [
            'model' => $model,
            'messages' => $messages,
        ];

        // Merge optional parameters
        $payload = array_merge($payload, $options);

        $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT_OPENAI, [
            'json' => $payload,
        ]);

        $data = $response->toArray();

        return OpenAICompletionResponse::fromArray($data);
    }
}
