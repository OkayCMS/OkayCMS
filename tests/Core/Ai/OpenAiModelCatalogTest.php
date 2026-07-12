<?php

declare(strict_types=1);

namespace Core\Ai;

use Okay\Core\Ai\OpenAiModelCatalog;
use PHPUnit\Framework\TestCase;

final class OpenAiModelCatalogTest extends TestCase
{
    public function testTextModelsIncludeModernFamiliesAndSkipNonTextModels(): void
    {
        $catalog = new OpenAiModelCatalog();

        $models = $catalog->textModels([
            ['id' => 'gpt-4o-mini', 'object' => 'model'],
            ['id' => 'o3-mini', 'object' => 'model'],
            ['id' => 'chatgpt-4o-latest', 'object' => 'model'],
            ['id' => 'text-embedding-3-small', 'object' => 'model'],
            ['id' => 'dall-e-3', 'object' => 'model'],
        ]);

        self::assertSame(
            ['chatgpt-4o-latest', 'gpt-4o-mini', 'o3-mini'],
            array_column($models, 'id')
        );
    }

    public function testSavedModelIsPreservedWhenLiveListOmitsIt(): void
    {
        $catalog = new OpenAiModelCatalog();

        $models = $catalog->textModels([
            ['id' => 'gpt-4o-mini', 'object' => 'model'],
        ], 'custom-saved-chat-model');

        self::assertContains('custom-saved-chat-model', array_column($models, 'id'));
    }
}
