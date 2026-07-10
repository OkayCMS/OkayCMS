<?php

namespace Okay\Modules\OkayCMS\Feeds\Controllers;

use Okay\Controllers\AbstractController;
use Okay\Core\Languages;
use Okay\Entities\LanguagesEntity;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;
use Okay\Modules\OkayCMS\Feeds\Helpers\FeedsHelper;

class FeedController extends AbstractController
{
    public function render(
        FeedsEntity $feedsEntity,
        FeedsHelper $feedsHelper,
        Languages   $languages,
        LanguagesEntity $languagesEntity,
        $url
    ) {
        $languagesList   = $languagesEntity->mappedBy('id')->find();
        $currentLanguage = $languagesList[$languages->getLangId()];

        $feed = $feedsEntity->findOne(['url' => $url]);
        if (empty($feed) || empty($currentLanguage->enabled) || (!$feed->enabled && empty($_SESSION['admin']))) {
            return false;
        }

        $feedsHelper->render($feed);

        return true;
    }
}