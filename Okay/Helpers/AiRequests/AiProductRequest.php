<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;

class AiProductRequest extends AbstractAiRequest
{
    public const ENTITY_TYPE = 'product';
    public const FIELD_META_TITLE = 'meta_title';
    public const FIELD_META_DESCRIPTION = 'meta_description';
    public const FIELD_META_KEYWORDS= 'meta_keywords';
    public const FIELD_ANNOTATION = 'annotation';
    public const FIELD_DESCRIPTION = 'description';

    private string $additionalInfo = '';

    public function __construct(?int $entityId, ?string $name)
    {
        parent::__construct($entityId, $name);

        if (!$this->entityId) {
            return;
        }
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        $featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $featuresEntity = $entityFactory->get(FeaturesEntity::class);

        $featuresValues = [];
        foreach ($featuresValuesEntity->find(['product_id' => $this->entityId]) as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }
        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresIds)) {
            $additionalInfo = [];
            foreach ($featuresEntity->find(['id' => $featuresIds]) as $f) {
                if (!empty($featuresValues[$f->id])) {
                    $values = [];
                    foreach ($featuresValues[$f->id] as $fv) {
                        $values[] = $fv->value;
                    }
                    $additionalInfo[] = sprintf(
                        '%s: %s',
                        $f->name,
                        implode(', ', $values)
                    );
                }
            }
            $this->additionalInfo = implode("\n", $additionalInfo);
        }
    }

    public function getRequestText(string $field): string
    {
        $template = '';
        switch ($field) {
            case self::FIELD_META_TITLE:
                $template = $this->settings->get('ai_product_title_template');
                break;
            case self::FIELD_META_DESCRIPTION:
                $template = $this->settings->get('ai_product_meta_description_template');
                break;
            case self::FIELD_META_KEYWORDS:
                $template = $this->settings->get('ai_product_keywords_template');
                break;
            case self::FIELD_ANNOTATION:
                $template = $this->settings->get('ai_product_annotation_template');
                break;
            case self::FIELD_DESCRIPTION:
                $template = $this->settings->get('ai_product_description_template');
        }
        return strtr($template, [
            '{$product}' => "\n'{$this->name}'\n"
        ]);
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}