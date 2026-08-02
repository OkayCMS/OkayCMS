<?php

declare(strict_types=1);

namespace Okay\Core\Ai;

interface AiTextClient
{
    /**
     * @param list<array{role: string, content: string}> $messages
     * @param array<string, float|int|string> $options
     */
    public function createText(string $model, array $messages, array $options = []): ?string;

    /**
     * @param list<array{role: string, content: string}> $messages
     * @param array<string, float|int|string> $options
     * @param callable(string): void $onDelta
     */
    public function streamText(string $model, array $messages, array $options, callable $onDelta): void;

    /**
     * @return list<array<string, mixed>>
     */
    public function listTextModels(?string $savedModelId = null): array;
}
