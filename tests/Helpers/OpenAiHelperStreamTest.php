<?php

declare(strict_types=1);

namespace Helpers;

use OpenAI\Responses\Responses\CreateStreamedResponse;
use OpenAI\Testing\ClientFake;
use Okay\Core\Ai\OpenAiTextClient;
use Okay\Core\Response;
use Okay\Core\Settings;
use Okay\Helpers\OpenAiHelper;
use PHPUnit\Framework\TestCase;

final class OpenAiHelperStreamTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__ . '/../../Okay/Core/config/constants.php';
    }

    public function testStreamMetadataEmitsDataChunksAndStopEvent(): void
    {
        $streams = [];
        $helper = new OpenAiHelper(
            $this->response($streams),
            $this->settings(),
            new OpenAiTextClient('test-key', new ClientFake([
                CreateStreamedResponse::fake($this->responseStream(['Hello', 'Line 1' . "\n" . 'Line 2'])),
            ]))
        );

        $helper->streamMetadata('User prompt', 'Assistant context');

        self::assertContains('data: Hello', $streams);
        self::assertContains("data: Line 1\ndata: Line 2", $streams);
        self::assertSame("event: stop\ndata: stopped\n\n", $streams[array_key_last($streams)]);
    }

    public function testStreamMetadataFormatsParagraphModeAndTerminates(): void
    {
        $streams = [];
        $helper = new OpenAiHelper(
            $this->response($streams),
            $this->settings(),
            new OpenAiTextClient('test-key', new ClientFake([
                CreateStreamedResponse::fake($this->responseStream(['First' . "\n" . 'Second'])),
            ]))
        );

        $helper->streamMetadata('User prompt', '', true);

        self::assertSame('data: <p>', $streams[0]);
        self::assertContains("data: First\ndata: Second</p><p>", $streams);
        self::assertContains('data: </p>', $streams);
        self::assertSame("event: stop\ndata: stopped\n\n", $streams[array_key_last($streams)]);
    }

    public function testClientFailureIsStreamedAndStillStops(): void
    {
        $streams = [];
        $helper = new OpenAiHelper(
            $this->response($streams),
            $this->settings(),
            new OpenAiTextClient('', new ClientFake())
        );

        $helper->streamMetadata('User prompt');

        self::assertContains('data: OpenAI API key is not configured.', $streams);
        self::assertSame("event: stop\ndata: stopped\n\n", $streams[array_key_last($streams)]);
    }

    /**
     * @param list<string> $deltas
     * @return resource
     */
    private function responseStream(array $deltas)
    {
        $stream = fopen('php://temp', 'r+');
        self::assertIsResource($stream);

        foreach ($deltas as $index => $delta) {
            fwrite($stream, 'data: ' . json_encode([
                'type' => 'response.output_text.delta',
                'item_id' => 'msg_1',
                'output_index' => 0,
                'content_index' => 0,
                'delta' => $delta,
                'sequence_number' => $index + 1,
            ], JSON_THROW_ON_ERROR) . "\n");
        }
        fwrite($stream, "data: [DONE]\n");
        rewind($stream);

        return $stream;
    }

    /**
     */
    private function response(array &$streams): Response
    {
        return new class ($streams) extends Response {
            /**
             * @param list<string> $streams
             */
            public function __construct(private array &$streams)
            {
            }

            public function setContentType(string $type): Response
            {
                return $this;
            }

            public function sendHeaders(): Response
            {
                return $this;
            }

            public function sendStream(string $content, ?string $type = null): void
            {
                $this->streams[] = $content;
            }
        };
    }

    private function settings(): Settings
    {
        return new class extends Settings {
            public function __construct()
            {
            }

            public function get($param)
            {
                return [
                    'open_ai_model' => 'gpt-4o-mini',
                    'open_ai_max_tokens' => 1000,
                    'open_ai_temperature' => 1.0,
                    'open_ai_frequency_penalty' => 0,
                    'open_ai_presence_penalty' => 0,
                    'ai_system_message' => 'System prompt',
                ][$param] ?? null;
            }
        };
    }
}
