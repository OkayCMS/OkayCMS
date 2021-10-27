<?php

namespace Okay\Modules\OkayCMS\Feeds\Helpers;

use Okay\Modules\OkayCMS\Feeds\Core\Presets\PresetAdapterFactory;

class FeedsHelper
{
    /** @var PresetAdapterFactory */
    private $presetAdapterFactory;

    public function __construct(
        PresetAdapterFactory $presetAdapterFactory
    ) {
        $this->presetAdapterFactory = $presetAdapterFactory;
    }

    public function render(object $feed): void
    {
        $adapter = $this->presetAdapterFactory->get($feed->preset);
        $adapter->render($feed);
    }
}