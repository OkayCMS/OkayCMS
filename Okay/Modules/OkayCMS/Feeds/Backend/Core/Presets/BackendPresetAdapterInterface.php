<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

interface BackendPresetAdapterInterface
{
    public function postSettings(): array;

    public function postCategorySettings(): array;

    public function loadSettings(array $settings): array;

    public function fetchSettingsTemplate(): string;

    public function registerCategorySettingsBlock(): void;

    public function registerFeatureSettingsBlock(): void;
}