<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

use Okay\Core\Design;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Feeds\Core\InheritedExtenderTrait;

abstract class AbstractBackendPresetAdapter implements BackendPresetAdapterInterface
{
    use InheritedExtenderTrait;

    /** @var string */
    protected static $settingsTemplate;


    /** @var Design */
    protected $design;

    /** @var Request */
    protected $request;

    public function __construct(
        Design  $design,
        Request $request
    ) {
        $this->design  = $design;
        $this->request = $request;
    }

    public function loadSettings(string $dbSettings)
    {
        $settings = unserialize($dbSettings);

        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    protected function getSettingsTemplate(): string
    {
        return $this->inheritedExtender(__FUNCTION__, static::$settingsTemplate, func_get_args());
    }

    public function fetchSettingsTemplate(): string
    {
        $settingsTemplate = $this->getSettingsTemplate();

        if (is_null($settingsTemplate)) {
            throw new \Exception('Backend adapter must contain valid settings template');
        }

        $settingsTemplate = $this->design->fetch($settingsTemplate);

        return $this->inheritedExtender(__FUNCTION__, $settingsTemplate, func_get_args());
    }
}