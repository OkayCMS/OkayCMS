<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Helpers\OpenAiEntityHelper;

class AiBrandRequest extends AbstractAiRequest
{
    /* Settings $settings*/
    private Settings $settings;

    /* OpenAiEntityHelper $openAiEntityHelper */
    private OpenAiEntityHelper $openAiEntityHelper;

    protected ?array $parts;
    protected ?int $entityId;
    protected string $additionalInfo = '';

    public function __construct(?int $entityId, ?array $parts, ?string $name, ?array $additionalInfoData)
    {
        parent::__construct($entityId, $parts, $name, $additionalInfoData);

        if (!$this->entityId) {
            return;
        }

        $this->parts = $parts;
        $this->entityId = $entityId;

        $this->setAdditionalInfo($additionalInfoData);

        $SL = ServiceLocator::getInstance();
        $this->openAiEntityHelper = $SL->getService(OpenAiEntityHelper::class);

        $this->settings = $SL->getService(Settings::class);
    }

    public function getRequestText(string $field): string
    {
        if (empty($field)) {
            return ExtenderFacade::execute(__METHOD__, '', func_get_args());
        }

        switch ($field) {
            case 'meta_title':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_meta_title', null, '');
                break;
            case 'meta_keywords':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_meta_keywords', null, '');
                break;
            case 'meta_description':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_meta_description', null, '');
                break;
            case 'name_h1':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_meta_h1', null, '');
                break;
            case 'annotation':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_annotation', null, '');
                break;
            case 'description':
                $pattern = $this->settings->get('settings_open_ai_patterns_brand_description', null, '');
                break;
        }

        if (!empty($pattern)) {
            $resultData = $this->openAiEntityHelper->compileMetadata($pattern, $this->parts);

            return ExtenderFacade::execute(__METHOD__, $resultData, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, '', func_get_args());
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo($additionalInfoData = []): string
    {
        $this->additionalInfo = implode("\n", $additionalInfoData);

        return ExtenderFacade::execute(__METHOD__, $this->additionalInfo, func_get_args());
    }
}