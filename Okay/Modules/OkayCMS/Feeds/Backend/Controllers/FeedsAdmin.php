<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Feeds\Backend\Helpers\BackendFeedsHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Requests\BackendFeedsRequest;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;

class FeedsAdmin extends IndexAdmin
{
    public function fetch(
        BackendFeedsRequest $feedsRequest,
        BackendFeedsHelper  $backendFeedsHelper,
        FeedsEntity         $feedsEntity
    ) {
        if ($this->request->method('post')) {
            $positions = $feedsRequest->postPositions();
            $backendFeedsHelper->sortPositions($positions);

            $ids    = $feedsRequest->postCheck();
            $action = $feedsRequest->postAction();
            if (is_array($ids)) {
                switch ($action) {
                    case 'disable': {
                        $backendFeedsHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendFeedsHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        $backendFeedsHelper->delete($ids);
                        break;
                    }
                    case 'duplicate': {
                        $backendFeedsHelper->duplicate($ids);
                        break;
                    }
                }
            }
        }

        $filter = $backendFeedsHelper->buildFilter();

        $productsCount = $feedsEntity->count($filter);
        if ($filter['limit'] > 0) {
            $pagesCount = ceil($productsCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }
        $filter['page'] = min($filter['page'], $pagesCount);

        $feeds   = $feedsEntity->find($filter);
        $presets = $backendFeedsHelper->getPresets();

        $this->design->assign('feeds', $feeds);
        $this->design->assign('presets', $presets);
        $this->design->assign('current_page',   $filter['page']);
        $this->design->assign('products_count', $productsCount);
        $this->design->assign('pages_count',    $pagesCount);
        $this->design->assign('current_limit',  $filter['limit']);

        $this->response->setContent($this->design->fetch('feeds.tpl'));
    }
}