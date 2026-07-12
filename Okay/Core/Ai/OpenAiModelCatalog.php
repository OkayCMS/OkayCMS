<?php

declare(strict_types=1);

namespace Okay\Core\Ai;

final class OpenAiModelCatalog
{
    /**
     * @param list<array<string, mixed>> $models
     * @return list<array<string, mixed>>
     */
    public function textModels(array $models, ?string $savedModelId = null): array
    {
        $textModels = [];
        $seen = [];

        foreach ($models as $model) {
            $id = $model['id'] ?? null;
            if (!is_string($id) || !$this->isTextModel($id)) {
                continue;
            }

            $textModels[] = $model;
            $seen[$id] = true;
        }

        if ($savedModelId !== null && $savedModelId !== '' && !isset($seen[$savedModelId])) {
            array_unshift($textModels, [
                'id' => $savedModelId,
                'object' => 'model',
                'created' => null,
                'owned_by' => null,
            ]);
        }

        usort(
            $textModels,
            static fn (array $left, array $right): int => strcmp((string) $left['id'], (string) $right['id'])
        );

        return $textModels;
    }

    private function isTextModel(string $modelId): bool
    {
        $id = strtolower($modelId);

        if (preg_match('/(?:embedding|audio|tts|whisper|dall-e|image|moderation|realtime)/', $id) === 1) {
            return false;
        }

        return str_starts_with($id, 'gpt-')
            || str_starts_with($id, 'o1')
            || str_starts_with($id, 'o3')
            || str_starts_with($id, 'o4')
            || str_starts_with($id, 'chatgpt-');
    }
}
