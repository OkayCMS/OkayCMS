<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets;

interface PresetAdapterInterface
{
    public function render($feed): void;
}