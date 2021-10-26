<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Helpers\BackendFeedsHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Requests\BackendFeedsRequest;

class FeedAdmin extends IndexAdmin
{
    public function fetch(
        BackendFeedsRequest $feedsRequest,
        BackendFeedsHelper  $backendFeedsHelper
    ) {
        if ($this->request->method('POST')) {
            $feed           = $feedsRequest->postFeed();
            $conditions     = $feedsRequest->postConditions();
            $newConditions  = $feedsRequest->postNewConditions();

            if ($error = $backendFeedsHelper->getValidateError($feed)) {
                $this->design->assign('message_error', $error);
            } else {

                if (empty($feed->id)) {
                    $feed     = $backendFeedsHelper->prepareAdd($feed);
                    $feed->id = $backendFeedsHelper->add($feed);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($feed->id);
                } else {
                    $feed = $backendFeedsHelper->prepareUpdate($feed);
                    $backendFeedsHelper->update($feed->id, $feed);
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                $backendFeedsHelper->updateConditions($feed->id, $conditions);
                $backendFeedsHelper->addConditions($feed->id, $newConditions);

                if (!$this->design->getVar('message_error')) {
                    $this->postRedirectGet->redirect();
                }
            }
        } else {
            $id = $feedsRequest->getId();
            $feed = $backendFeedsHelper->getFeed($id);
        }

        $categories        = $backendFeedsHelper->getCategories();
        $brands            = $backendFeedsHelper->getBrands();
        $features          = $backendFeedsHelper->getFeatures();
        $presets           = $backendFeedsHelper->getPresets();

        $this->design->assign('categories', $categories);
        $this->design->assign('brands', $brands);
        $this->design->assign('features', $features);
        $this->design->assign('presets', $presets);

        $settingsTemplates = $backendFeedsHelper->fetchSettingsTemplates();
        $this->design->assign('settings_templates', $settingsTemplates);

        if ($feed) {
            $presetName = $feed->preset;

            $feed->settings   = $backendFeedsHelper->loadSettings($presetName, $feed->settings);
            $conditions       = $backendFeedsHelper->getConditions($feed->id);

            $this->design->assign('feed', $feed);
            $this->design->assign('conditions', $conditions);

            $settingsTemplate = $backendFeedsHelper->fetchSettingsTemplate($presetName);
        } else {
            $presetName = array_keys($presets)[0];

            $settingsTemplate = $backendFeedsHelper->fetchSettingsTemplate($presetName);
        }
        $backendFeedsHelper->registerSettingsBlocks($presetName);

        $this->design->assign('settings_template', $settingsTemplate);

        $this->response->setContent($this->design->fetch('feed.tpl'));
    }

    public function getFeatureValues(
        BackendFeedsHelper $backendFeedsHelper
    ) {
        $featureId = $this->request->get('feature_id', 'int');
        $featureValues = $backendFeedsHelper->getFeatureValues($featureId);

        $this->response->setContent(json_encode($featureValues), RESPONSE_JSON);
    }

    public function getSubCategories(
        BackendCategoriesHelper $categoriesHelper,
        BackendFeedsRequest $feedsRequest,
        BackendFeedsHelper $backendFeedsHelper
    ) {
        $feedId = $feedsRequest->getId();
        $feed = $backendFeedsHelper->getFeed($feedId);

        $backendFeedsHelper->registerSettingsBlocks($feed->preset);

        $result = [];
        /*Выборка категории и её деток*/
        if ($this->request->get("category_id")) {
            $categoryId = $this->request->get("category_id", 'integer');
            $categories = $categoriesHelper->getCategory($categoryId);
            $this->design->assign('feed', $feed);
            $this->design->assign('categories', $categories->subcategories);
            $result['success'] = true;
            $result['cats'] = $this->design->fetch("feed_tabs/categories_ajax.tpl");
        } else {
            $result['success ']= false;
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    public function getAllCategories(
        BackendCategoriesHelper $categoriesHelper,
        BackendFeedsRequest $feedsRequest,
        BackendFeedsHelper $backendFeedsHelper
    ) {
        $feedId = $feedsRequest->getId();
        $feed = $backendFeedsHelper->getFeed($feedId);

        $backendFeedsHelper->registerSettingsBlocks($feed->preset);

        $this->design->assign('feed', $feed);
        $this->design->assign('categories', $categoriesHelper->getCategoriesTree());
        $this->design->assign('isAllCategories', true);
        $this->design->assign('level', 1);

        $result['success'] = true;
        $result['cats'] = $this->design->fetch("feed_tabs/categories_ajax.tpl");
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    public function updateEntitySettings(
        BackendFeedsRequest $feedsRequest,
        BackendFeedsHelper  $backendFeedsHelper
    ) {
        if (!$this->request->method('post')) {
            return false;
        }

        switch($this->request->post('entity')) {
            case 'category':
                $settings = $feedsRequest->postCategorySettings();
                $backendFeedsHelper->updateCategorySettings($this->request->post('feed_id'), $this->request->post('entity_id'), $settings);
                break;

            case 'feature':
                $settings = $feedsRequest->postFeatureSettings();
                $backendFeedsHelper->updateFeatureSettings($this->request->post('feed_id'), $this->request->post('entity_id'), $settings);
                break;
        }

        $this->response->setContent(json_encode(['success' => 1]), RESPONSE_JSON);
    }
}