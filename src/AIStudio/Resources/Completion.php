<?php

namespace AIStudio\Resources;

use AIStudio\Client;
use AIStudio\Models\CompletionOptions;
use AIStudio\Models\Message;

class Completion
{
    private const ENDPOINT = '/foundationModels/v1/completion';
    
    public function __construct(
        private Client $client
    ) {}
    
    /**
     * @param Message[] $messages
     */
    public function create(
        string $modelUri,
        array $messages,
        ?CompletionOptions $options = null
    ): array {
        $options = $options ?? new CompletionOptions();
        
        $payload = [
            'modelUri' => $modelUri,
            'completionOptions' => $options->toArray(),
            'messages' => array_map(fn($msg) => $msg->toArray(), $messages),
        ];
        
        $response = $this->client->getHttpClient()->request('POST', self::ENDPOINT, [
            'json' => $payload,
        ]);
        
        return json_decode($response->getContent(), true);
    }
}
