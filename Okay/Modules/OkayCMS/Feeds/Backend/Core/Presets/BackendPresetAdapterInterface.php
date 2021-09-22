<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

interface BackendPresetAdapterInterface
{
    public function postSettings(): string;

    public function loadSettings(string $dbSettings);

    public function fetchSettingsTemplate(): string;
}