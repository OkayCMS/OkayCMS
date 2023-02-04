<?php

namespace Okay\Modules\OkayCMS\Feeds\Helpers;

use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Helpers\MainHelper;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\PresetAdapterFactory;

class FeedsHelper
{
    /** @var PresetAdapterFactory $presetAdapterFactory */
    private $presetAdapterFactory;

    /** @var MainHelper $mainHelper */
    private $mainHelper;

    private $uaLang;

    public function __construct(
        PresetAdapterFactory $presetAdapterFactory,
        MainHelper $mainHelper
    ) {
        $this->presetAdapterFactory = $presetAdapterFactory;
        $this->mainHelper = $mainHelper;

        $this->languages     = $mainHelper->getAllLanguages();
        $this->firstLanguage = reset($this->languages);
        $this->language      = $mainHelper->getCurrentLanguage();

        foreach ($this->languages as $lang) {
            if ($lang->label == 'ua') {
                $this->uaLang = $lang;
            }
        }
    }

    public function render(object $feed): void
    {
        $adapter = $this->presetAdapterFactory->get($feed->preset);
        $adapter->render($feed);
    }

    public function checkIfUaMainLanguageIs()
    {
        if ((!empty($this->uaLang))
            && ($this->uaLang->enabled == 1)       //  если UA активный
            && ($this->language->label != 'ua')     //  если UA не текущий
        ) {
            return $this->uaLang;
        }

        return false;
    }
}