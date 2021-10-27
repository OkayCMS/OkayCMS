<?php

namespace Okay\Modules\OkayCMS\Feeds\Controllers;

use Okay\Controllers\AbstractController;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;
use Okay\Modules\OkayCMS\Feeds\Helpers\FeedsHelper;

class FeedController extends AbstractController
{
    public function render(
        FeedsEntity $feedsEntity,
        FeedsHelper $feedsHelper,
        $url
    ) {
        $feed = $feedsEntity->findOne(['url' => $url]);
        if (empty($feed) || (!$feed->enabled && empty($_SESSION['admin']))) {
            return false;
        }

        $feedsHelper->render($feed);

        return true;
    }
}