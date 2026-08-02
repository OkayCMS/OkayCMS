<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

interface BackendPresetAdapterInterface
{
    /**
     * @return array<string, mixed>
     */
    public function postSettings(): array;

    /**
     * @return array<string, mixed>
     */
    public function postCategorySettings(): array;

    /**
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public function loadSettings(array $settings): array;

    public function fetchSettingsTemplate(): string;

    public function registerCategorySettingsBlock(): void;

    public function registerFeatureSettingsBlock(): void;
}
