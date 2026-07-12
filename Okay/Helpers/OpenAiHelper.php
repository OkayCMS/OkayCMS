<?php

namespace Okay\Helpers;

use Okay\Core\Ai\OpenAiTextClient;
use Okay\Core\Response;
use Okay\Core\Settings;
use RuntimeException;

class OpenAiHelper
{
    private const DEFAULT_MODEL = 'gpt-4o-mini';

    private OpenAiTextClient $aiTextClient;
    private Response $response;

    private string $model;
    private float $temperature;
    private float $frequencyPenalty;
    private float $presencePenalty;
    private int $maxTokens;
    private Settings $settings;

    public function __construct(
        Response $response,
        Settings $settings,
        OpenAiTextClient $aiTextClient
    ) {
        $this->settings = $settings;
        $this->response = $response;
        $this->aiTextClient = $aiTextClient;
        $this->model = ((string)$settings->get('open_ai_model')) ?: self::DEFAULT_MODEL;
        $this->maxTokens = ((int)$settings->get('open_ai_max_tokens')) ?: 1000;
        $this->temperature = ((float)$settings->get('open_ai_temperature')) ?: 1.0;
        $this->frequencyPenalty = ((float)$settings->get('open_ai_frequency_penalty')) ?: 0;
        $this->presencePenalty = ((float)$settings->get('open_ai_presence_penalty')) ?: 0;
    }

    public function streamMetadata(string $userMessage, string $assistantMessage = '', bool $format = false): void
    {
        $this->response->setContentType(RESPONSE_GPT_STREAM);
        $this->response->sendHeaders();
        if ($format) {
            $this->response->sendStream('data: <p>');
        }
        ignore_user_abort(true);

        try {
            $this->aiTextClient->streamText(
                $this->model,
                $this->messages($userMessage, $assistantMessage),
                $this->options(),
                function (string $content) use ($format): void {
                    if ($content === '') {
                        return;
                    }
                    if ($format && trim($content) !== '' && strpos($content, "\n") !== false) {
                        $content = trim($content) . '</p><p>';
                    }
                    $this->sendData($content);
                    if (connection_aborted()) {
                        return;
                    }
                }
            );
        } catch (RuntimeException $exception) {
            $this->sendData($exception->getMessage());
        }

        if ($format) {
            $this->sendData('</p>');
        }
        $this->response->sendStream("event: stop\ndata: stopped\n\n");
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function messages(string $userMessage, string $assistantMessage = ''): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => (string)$this->settings->get('ai_system_message'),
            ],
            [
                'role' => 'user',
                'content' => $userMessage,
            ]
        ];

        if (!empty($assistantMessage)) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $assistantMessage
            ];
        }

        return $messages;
    }

    /**
     * @return array<string, float|int>
     */
    private function options(): array
    {
        return [
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty' => $this->presencePenalty,
        ];
    }

    /**
     * @return non-empty-string
     */
    private function sseDataFrame(string $content): string
    {
        $lines = explode("\n", str_replace("\r", '', $content));

        return implode("\n", array_map(static fn (string $line): string => 'data: ' . $line, $lines));
    }

    private function sendData(string $content): void
    {
        $this->response->sendStream($this->sseDataFrame($content));
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    public function getTextModels(): ?array
    {
        try {
            return $this->aiTextClient->listTextModels($this->model);
        } catch (RuntimeException) {
            return [
                ['id' => $this->model],
            ];
        }
    }
}
