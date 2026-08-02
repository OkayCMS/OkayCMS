<?php

namespace Okay\Helpers;

use Okay\Core\Response;
use Okay\Core\Settings;
use Orhanerday\OpenAi\OpenAi;

class OpenAiHelper
{
    private OpenAi $openAi;
    private Response $response;

    private string $model;
    private float $temperature;
    private float $frequencyPenalty;
    private float $presencePenalty;
    private int $maxTokens;
    private Settings $settings;

    public function __construct(
        Response $response,
        Settings $settings
    ) {
        $this->settings = $settings;
        $this->response = $response;
        $this->openAi = new OpenAi((string)$settings->get('open_ai_api_key'));
        $this->model = ((string)$settings->get('open_ai_model')) ?: 'gpt-3.5-turbo';
        $this->maxTokens = ((int)$settings->get('open_ai_max_tokens')) ?: 1000;
        $this->temperature = $this->floatSetting('open_ai_temperature', 1.0);
        $this->frequencyPenalty = $this->floatSetting('open_ai_frequency_penalty', 0.0);
        $this->presencePenalty = $this->floatSetting('open_ai_presence_penalty', 0.0);
    }

    public function streamMetadata(string $userMessage, string $contextMessage = '', bool $format = false)
    {
        $this->response->setContentType(RESPONSE_GPT_STREAM);
        $this->response->sendHeaders();
        if ($format) {
            $this->response->sendStream('data: <p>');
        }
        ignore_user_abort(true);

        $hasError = false;
        $this->aiChat(
            $userMessage,
            $contextMessage,
            function ($ch, $data) use ($format, &$hasError) {
                $deltas = explode("\n", $data);
                foreach ($deltas as $data2) {
                    if (strpos($data2, 'data: ') !== 0) {
                        continue;
                    }
                    $json = json_decode(substr($data2, 6));
                    if (json_last_error() && trim($data2) != 'data: [DONE]') {
                        continue;
                    }
                    if (isset($json->error->message)) {
                        $hasError = true;
                        $this->sendError((string)$json->error->message);
                        return 0;
                    }
                    if (isset($json->choices[0]->delta)) {
                        $content = $json->choices[0]->delta->content ?? '';
                    } elseif (trim($data2) == 'data: [DONE]') {
                        $content = '';
                    } else {
                        $content = '';
                    }

                    if ($content === '') {
                        continue;
                    }

                    if ($format && !empty(trim($content)) && strpos($content, "\n") !== false) {
                        $content = trim($content) . '</p><p>';
                    }

                    $this->response->sendStream('data: ' . $content);
                    if (connection_aborted()) {
                        return 0;
                    }
                }
                return strlen($data);
            }
        );

        if (!$hasError && $format) {
            $this->response->sendStream('data: </p>');
        }
        $this->response->sendStream("event: stop\ndata: stopped\n\n");
    }

    private function aiChat(string $userMessage, string $contextMessage = '', ?callable $stream = null): ?string
    {
        $content = $userMessage;
        if ($contextMessage !== '') {
            $content .= "\n\n" . $contextMessage;
        }

        $messages = [
            [
                "role" => "system",
                "content" => (string)$this->settings->get('ai_system_message'),
            ],
            [
                "role" => "user",
                "content" => $content,
            ]
        ];

        $chat = $this->openAi->chat([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty' => $this->presencePenalty,
            'stream' => !empty($stream),
        ], $stream);

        if (empty($stream)) {
            $response = json_decode($chat);
            return $response->choices[0]->message->content ?? null;
        }
        return null;
    }

    private function sendError(string $message): void
    {
        $lines = explode("\n", str_replace("\r", '', $message));
        $payload = implode("\n", array_map(static function (string $line): string {
            return 'data: ' . $line;
        }, $lines));
        $this->response->sendStream("event: error\n" . $payload . "\n\n");
    }

    private function floatSetting(string $param, float $default): float
    {
        $raw = $this->settings->get($param);
        if ($raw === null || $raw === '') {
            return $default;
        }

        return (float)$raw;
    }

    private function getModels(): ?array
    {
        $response = $this->openAi->listModels();

        $models = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($models['data'])) {
            return $models['data'];
        }

        return null;
    }

    public function getTextModels(): ?array
    {
        $models = $this->getModels();
        if ($models === null) {
            return null;
        }

        $textModels = array_filter($models, function ($model) {
            return strpos($model['id'], 'gpt-') !== false;
        });

        return $textModels;
    }
}
