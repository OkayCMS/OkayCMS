<?php

namespace Okay\Modules\OkayCMS\Feeds\Helpers;

use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Helpers\MainHelper;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\PresetAdapterFactory;

/**
 * @phpstan-type LanguageRow object{label: string, enabled: int|string|bool|null}
 * @phpstan-type FeedRow object{preset: string}
 */
class FeedsHelper
{
    /** @var PresetAdapterFactory $presetAdapterFactory */
    private $presetAdapterFactory;

    /** @var MainHelper $mainHelper */
    private $mainHelper;

    /** @var LanguageRow|null */
    private $uaLang;

    /** @var LanguageRow|null */
    private $language;

    public function __construct(
        PresetAdapterFactory $presetAdapterFactory,
        MainHelper $mainHelper
    ) {
        $this->presetAdapterFactory = $presetAdapterFactory;
        $this->mainHelper = $mainHelper;

        /** @var array<int|string, LanguageRow> $languages */
        $languages = $mainHelper->getAllLanguages();
        /** @var LanguageRow|null $language */
        $language = $mainHelper->getCurrentLanguage();
        $this->language = $language;

        foreach ($languages as $lang) {
            if ($lang->label == 'ua') {
                $this->uaLang = $lang;
            }
        }
    }

    /**
     * @param FeedRow $feed
     */
    public function render(object $feed): void
    {
        $adapter = $this->presetAdapterFactory->get($feed->preset);
        $adapter->render($feed);
    }

    public function checkIfUaMainLanguageIs()
    {
        if (
            (!empty($this->uaLang))
            && ($this->uaLang->enabled == 1)       //  если UA активный
            && ($this->language !== null)
            && ($this->language->label != 'ua')     //  если UA не текущий
        ) {
            return $this->uaLang;
        }

        return false;
    }
}
