<?php

declare(strict_types=1);

namespace Core\Ai;

use OpenAI\Responses\Models\ListResponse;
use OpenAI\Responses\Responses\CreateResponse;
use OpenAI\Responses\Responses\CreateStreamedResponse;
use OpenAI\Testing\ClientFake;
use Okay\Core\Ai\OpenAiTextClient;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class OpenAiTextClientTest extends TestCase
{
    public function testStreamedResponsesTextDeltasAreEmittedInOrder(): void
    {
        $stream = fopen('php://temp', 'r+');
        self::assertIsResource($stream);

        fwrite($stream, implode("\n", [
            'data: {"type":"response.output_text.delta","item_id":"msg_1","output_index":0,"content_index":0,"delta":"Hello","sequence_number":1}',
            'data: {"type":"response.output_text.delta","item_id":"msg_1","output_index":0,"content_index":0,"delta":" world","sequence_number":2}',
            'data: [DONE]',
            '',
        ]));
        rewind($stream);

        $fake = new ClientFake([
            CreateStreamedResponse::fake($stream),
        ]);
        $client = new OpenAiTextClient('test-key', $fake);

        $chunks = [];
        $client->streamText('gpt-4o-mini', $this->messages(), [], static function (string $delta) use (&$chunks): void {
            $chunks[] = $delta;
        });

        self::assertSame(['Hello', ' world'], $chunks);
    }

    public function testCreateTextReturnsResponsesOutputText(): void
    {
        $fake = new ClientFake([
            CreateResponse::fake(),
        ]);
        $client = new OpenAiTextClient('test-key', $fake);

        self::assertStringContainsString(
            'positive news story',
            (string) $client->createText('gpt-4o-mini', $this->messages())
        );
    }

    public function testListTextModelsFiltersThroughCatalogAndPreservesSavedModel(): void
    {
        $fake = new ClientFake([
            ListResponse::fake([
                'data' => [
                    ['id' => 'gpt-4o-mini', 'object' => 'model', 'created' => 1, 'owned_by' => 'openai'],
                    ['id' => 'o3-mini', 'object' => 'model', 'created' => 1, 'owned_by' => 'openai'],
                    ['id' => 'text-embedding-3-small', 'object' => 'model', 'created' => 1, 'owned_by' => 'openai'],
                ],
            ]),
        ]);
        $client = new OpenAiTextClient('test-key', $fake);

        $models = $client->listTextModels('saved-legacy-model');

        self::assertNotNull($models);
        self::assertSame(
            ['gpt-4o-mini', 'o3-mini', 'saved-legacy-model'],
            array_column($models, 'id')
        );
    }

    public function testEmptyApiKeyFailsBeforeMakingRequest(): void
    {
        $fake = new ClientFake();
        $client = new OpenAiTextClient('', $fake);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OpenAI API key is not configured.');

        try {
            $client->createText('gpt-4o-mini', $this->messages());
        } finally {
            $fake->assertNothingSent();
        }
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function messages(): array
    {
        return [
            ['role' => 'system', 'content' => 'System prompt'],
            ['role' => 'user', 'content' => 'Write metadata'],
            ['role' => 'assistant', 'content' => 'Existing metadata'],
        ];
    }
}
