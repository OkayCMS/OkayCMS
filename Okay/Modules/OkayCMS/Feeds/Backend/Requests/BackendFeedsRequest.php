<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Requests;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\BackendPresetAdapterFactory;

class BackendFeedsRequest
{
    /** @var Request */
    private $request;

    /** @var BackendPresetAdapterFactory */
    private $presetAdapterFactory;

    public function __construct(
        Request                     $request,
        BackendPresetAdapterFactory $backendPresetAdapterFactory
    ) {
        $this->request              = $request;
        $this->presetAdapterFactory = $backendPresetAdapterFactory;
    }

    public function postFeed(): object
    {
        $feed = (object) [
            'id'       => $this->request->post('id', 'integer'),
            'name'     => $this->request->post('name'),
            'url'      => trim($this->request->post('url')),
            'enabled'  => $this->request->post('enabled', 'boolean'),
            'preset'   => $this->request->post('preset', 'string')
        ];

        $feed->settings = $this->postSettings($feed->preset);

        return ExtenderFacade::execute(__METHOD__, $feed, func_get_args());
    }

    public function postSettings(string $presetName): array
    {
        $adapter = $this->presetAdapterFactory->get($presetName);
        $settings = $adapter->postSettings();

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function postCategorySettings(): array
    {
        $presetName = $this->request->post('preset');
        $adapter = $this->presetAdapterFactory->get($presetName);

        $settings = $adapter->postCategorySettings();

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function postFeatureSettings(): array
    {
        $presetName = $this->request->post('preset');
        $adapter = $this->presetAdapterFactory->get($presetName);

        $settings = $adapter->postFeatureSettings();

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function postConditions(): array
    {
        $conditions = $this->request->post('conditions', null, []);

        return ExtenderFacade::execute(__METHOD__, $conditions, func_get_args());
    }

    public function postNewConditions(): array
    {
        $newConditions = $this->request->post('new_conditions', null, []);

        return ExtenderFacade::execute(__METHOD__, $newConditions, func_get_args());
    }

    public function getId()
    {
        $id = $this->request->get('id', 'integer');

        return ExtenderFacade::execute(__METHOD__, $id, func_get_args());
    }

    public function postPositions(): array
    {
        $positions = $this->request->post('positions', null, []);

        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }

    public function postCheck(): array
    {
        $check = $this->request->post('check', null, []);

        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction(): string
    {
        $action = $this->request->post('action', null, []);

        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }
}