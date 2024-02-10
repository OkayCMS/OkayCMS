<?php

namespace Okay\Helpers;

use Okay\Core\Response;
use Orhanerday\OpenAi\OpenAi;

class OpenAiHelper
{
    private OpenAi $openAi;
    private Response $response;

    private string $model = 'gpt-3.5-turbo';
    private float $temperature = 1.0;
    private int $frequencyPenalty = 0;
    private int $presencePenalty = 0;
    private int $maxTokens = 1000;

    public function __construct(
        OpenAi $openAi,
        Response $response
    ) {
        $this->openAi = $openAi;
        $this->response = $response;
    }

    public function getMetadata(): ?string
    {
        return $this->aiChat(
            "Згенеруй мені унікальний текст для товару  на 800 символів\n 'Диван-ліжко Max 1,2 в сканині Кордрой' з такими ключовими словами.\nКупити у Дніпрі, найкраща ціна,безкоштовна доставка",
            "- Вага: 90кг. UA\n
             - Размеры: 150.9 х 115.2 х 87 cm\n
             - Материал: Буковые ламели\n
             - Производитель: Италия\n
             - Количество мест: 2\n
             - Высота сиденья: 160\n
             - Водонепронецаемый: да"
        );
    }

    public function streamMetadata(string $userMessage, string $assistantMessage = '')
    {
        $this->response->setContentType(RESPONSE_GPT_STREAM);
        $this->response->sendHeaders();
        $this->response->sendStream('data: <p>');
        ignore_user_abort(true);

        $this->aiChat(
            $userMessage,
            $assistantMessage,
            function ($ch, $data) {
                $deltas = explode("\n", $data);
                foreach ($deltas as $data2) {
                    if (strpos($data2, 'data: ') !== 0) {
                        continue;
                    }
                    $json = json_decode(substr($data2, 6));
                    if (json_last_error() && trim($data2) != 'data: [DONE]') {
                        continue;
                    }
                    if (isset($json->choices[0]->delta)) {
                        $content = $json->choices[0]->delta->content ?? '';
                    } elseif (isset($json->error->message)) {
                        $content = $json->error->message;
                    } elseif (trim($data2) == 'data: [DONE]') {
                        $content = '';
                    } else {
                        $content = '';
                    }

                    if (!empty(trim($content)) && strpos($content, "\n") !== false) {
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

        $this->response->sendStream('data: </p>');
        $this->response->sendStream("event: stop\ndata: stopped\n\n");
    }

    private function aiChat(string $userMessage, string $assistantMessage = '', ?callable $stream = null): ?string
    {
        $messages = [
            [
                "role" => "user",
                "content" => $userMessage
            ]
        ];

        if (!empty($assistantMessage)) {
            $messages[] = [
                "role" => "assistant",
                "content" => $assistantMessage
            ];
        }

        $chat = $this->openAi->chat([
            'model' => $this->model,
            'messages' => $messages,

//        [
//            "role" => "system",
//            "content" => "Пиши опис як наче ти пошукова система"
//        ],
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
}