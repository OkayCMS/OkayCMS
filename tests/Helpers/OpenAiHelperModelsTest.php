<?php

declare(strict_types=1);

namespace Helpers;

use OpenAI\Responses\Models\ListResponse;
use OpenAI\Testing\ClientFake;
use Okay\Core\Ai\OpenAiTextClient;
use Okay\Core\Response;
use Okay\Core\Settings;
use Okay\Helpers\OpenAiHelper;
use PHPUnit\Framework\TestCase;

final class OpenAiHelperModelsTest extends TestCase
{
    public function testGetTextModelsDelegatesToClientAndPreservesSelectedModel(): void
    {
        $helper = new OpenAiHelper(
            $this->response(),
            $this->settings('saved-model'),
            new OpenAiTextClient('test-key', new ClientFake([
                ListResponse::fake([
                    'data' => [
                        ['id' => 'gpt-4o-mini', 'object' => 'model', 'created' => 1, 'owned_by' => 'openai'],
                    ],
                ]),
            ]))
        );

        $models = $helper->getTextModels();

        self::assertNotNull($models);
        self::assertContains('saved-model', array_column($models, 'id'));
        self::assertContains('gpt-4o-mini', array_column($models, 'id'));
    }

    private function response(): Response
    {
        return new class extends Response {
            public function __construct()
            {
            }
        };
    }

    private function settings(string $model): Settings
    {
        return new class ($model) extends Settings {
            public function __construct(private readonly string $model)
            {
            }

            public function get($param)
            {
                return [
                    'open_ai_model' => $this->model,
                    'open_ai_max_tokens' => 1000,
                    'open_ai_temperature' => 1.0,
                    'open_ai_frequency_penalty' => 0,
                    'open_ai_presence_penalty' => 0,
                ][$param] ?? null;
            }
        };
    }
}
