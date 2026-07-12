<?php

declare(strict_types=1);

namespace Okay\Core\Ai;

use OpenAI;
use OpenAI\Contracts\ClientContract;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Responses\Chat\CreateStreamedResponse as ChatStreamedResponse;
use OpenAI\Responses\Responses\CreateStreamedResponse as ResponseStreamedResponse;
use OpenAI\Responses\Responses\Streaming\OutputTextDelta;
use Okay\Core\Settings;
use RuntimeException;
use Throwable;

final class OpenAiTextClient implements AiTextClient
{
    private ?ClientContract $client;
    private string $apiKey;

    public function __construct(
        string|Settings $apiKey,
        ?ClientContract $client = null,
        private readonly ?OpenAiModelCatalog $modelCatalog = null
    ) {
        $this->apiKey = $apiKey instanceof Settings ? (string) $apiKey->get('open_ai_api_key') : $apiKey;
        $this->client = $client;
    }

    public function createText(string $model, array $messages, array $options = []): ?string
    {
        $client = $this->client();

        try {
            if ($this->usesChatCompletions($model)) {
                $response = $client->chat()->create($this->chatPayload($model, $messages, $options));
                return $response->choices[0]->message->content ?? null;
            }

            return $client->responses()
                ->create($this->responsesPayload($model, $messages, $options))
                ->outputText;
        } catch (ErrorException $e) {
            throw new RuntimeException($this->safeErrorMessage($e), 0, $e);
        } catch (Throwable $e) {
            throw new RuntimeException('OpenAI request failed.', 0, $e);
        }
    }

    public function streamText(string $model, array $messages, array $options, callable $onDelta): void
    {
        $client = $this->client();

        try {
            if ($this->usesChatCompletions($model)) {
                foreach ($client->chat()->createStreamed($this->chatPayload($model, $messages, $options)) as $event) {
                    $this->emitChatDelta($event, $onDelta);
                }
                return;
            }

            foreach ($client->responses()->createStreamed($this->responsesPayload($model, $messages, $options)) as $event) {
                $this->emitResponseDelta($event, $onDelta);
            }
        } catch (ErrorException $e) {
            throw new RuntimeException($this->safeErrorMessage($e), 0, $e);
        } catch (Throwable $e) {
            throw new RuntimeException('OpenAI request failed.', 0, $e);
        }
    }

    public function listTextModels(?string $savedModelId = null): array
    {
        $client = $this->client();

        try {
            $models = array_values($client->models()->list()->toArray()['data']);
        } catch (ErrorException $e) {
            throw new RuntimeException($this->safeErrorMessage($e), 0, $e);
        } catch (Throwable $e) {
            throw new RuntimeException('OpenAI model list request failed.', 0, $e);
        }

        return ($this->modelCatalog ?? new OpenAiModelCatalog())->textModels($models, $savedModelId);
    }

    private function client(): ClientContract
    {
        if (trim($this->apiKey) === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        if ($this->client === null) {
            $this->client = OpenAI::client($this->apiKey);
        }

        return $this->client;
    }

    /**
     * @param list<array{role: string, content: string}> $messages
     * @param array<string, float|int|string> $options
     * @return array<string, mixed>
     */
    private function responsesPayload(string $model, array $messages, array $options): array
    {
        $instructions = null;
        $input = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $instructions = $message['content'];
                continue;
            }

            $input[] = [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }

        return $this->withOptions([
            'model' => $model,
            'instructions' => $instructions,
            'input' => $input,
        ], $this->responsesOptions($options));
    }

    /**
     * @param list<array{role: string, content: string}> $messages
     * @param array<string, float|int|string> $options
     * @return array<string, mixed>
     */
    private function chatPayload(string $model, array $messages, array $options): array
    {
        return $this->withOptions([
            'model' => $model,
            'messages' => $messages,
        ], $this->chatOptions($options));
    }

    /**
     * @param array<string, float|int|string> $options
     * @return array<string, mixed>
     */
    private function responsesOptions(array $options): array
    {
        $result = [];

        if (isset($options['temperature'])) {
            $result['temperature'] = $options['temperature'];
        }

        if (isset($options['max_tokens'])) {
            $result['max_output_tokens'] = $options['max_tokens'];
        }

        return $result;
    }

    /**
     * @param array<string, float|int|string> $options
     * @return array<string, mixed>
     */
    private function chatOptions(array $options): array
    {
        return array_intersect_key($options, array_flip([
            'temperature',
            'max_tokens',
            'frequency_penalty',
            'presence_penalty',
        ]));
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function withOptions(array $payload, array $options): array
    {
        return array_replace($payload, $options);
    }

    private function usesChatCompletions(string $model): bool
    {
        $id = strtolower($model);

        return str_starts_with($id, 'gpt-3.5-')
            || in_array($id, ['gpt-4', 'gpt-4-32k', 'gpt-4-turbo'], true);
    }

    private function emitResponseDelta(ResponseStreamedResponse $event, callable $onDelta): void
    {
        if ($event->response instanceof OutputTextDelta && $event->response->delta !== '') {
            $onDelta($event->response->delta);
        }
    }

    private function emitChatDelta(ChatStreamedResponse $event, callable $onDelta): void
    {
        foreach ($event->choices as $choice) {
            $content = $choice->delta->content ?? '';
            if ($content !== '') {
                $onDelta($content);
            }
        }
    }

    private function safeErrorMessage(ErrorException $exception): string
    {
        $message = $exception->getMessage();

        return $message !== '' ? $message : 'OpenAI request failed.';
    }
}
