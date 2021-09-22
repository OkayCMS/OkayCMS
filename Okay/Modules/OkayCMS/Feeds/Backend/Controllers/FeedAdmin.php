<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;
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
            $mappings       = $feedsRequest->postMappings();
            $newMappings    = $feedsRequest->postNewMappings();

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

                $backendFeedsHelper->updateMappings($feed->id, $mappings);
                $backendFeedsHelper->addMappings($feed->id, $newMappings);

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
        $settingsTemplates = $backendFeedsHelper->fetchSettingsTemplates();

        $this->design->assign('categories', $categories);
        $this->design->assign('brands', $brands);
        $this->design->assign('features', $features);
        $this->design->assign('presets', $presets);
        $this->design->assign('settings_templates', $settingsTemplates);

        if ($feed) {
            $feed->settings   = $backendFeedsHelper->loadSettings($feed->preset, $feed->settings);
            $conditions       = $backendFeedsHelper->getConditions($feed->id);
            $featureMappings  = $backendFeedsHelper->getFeatureMappings($feed->id);
            $categoryMappings = $backendFeedsHelper->getCategoryMappings($feed->id);

            $this->design->assign('feed', $feed);
            $this->design->assign('conditions', $conditions);
            $this->design->assign('feature_mappings', $featureMappings);
            $this->design->assign('category_mappings', $categoryMappings);

            $settingsTemplate = $backendFeedsHelper->fetchSettingsTemplate($feed->preset);
        } else {
            $settingsTemplate = $backendFeedsHelper->fetchSettingsTemplate(array_keys($presets)[0]);
        }
        $this->design->assign('settings_template', $settingsTemplate);

        $this->response->setContent($this->design->fetch('feed.tpl'));
    }

    public function getFeatureValues(
        BackendFeedsHelper  $backendFeedsHelper
    ) {
        $featureId = $this->request->get('feature_id', 'int');
        $featureValues = $backendFeedsHelper->getFeatureValues($featureId);

        $this->response->setContent(json_encode($featureValues), RESPONSE_JSON);
    }
}