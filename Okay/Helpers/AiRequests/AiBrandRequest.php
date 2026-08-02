<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\BrandsEntity;

class AiBrandRequest extends AbstractAiRequest
{
    public const ENTITY_TYPE = 'brand';
    public const FIELD_META_TITLE = 'meta_title';
    public const FIELD_META_DESCRIPTION = 'meta_description';
    public const FIELD_META_KEYWORDS= 'meta_keywords';
    public const FIELD_ANNOTATION = 'annotation';
    public const FIELD_DESCRIPTION = 'description';

    private string $annotation = '';
    private string $description = '';
    private string $additionalInfo = '';

    public function __construct(?int $entityId, ?string $name)
    {
        parent::__construct($entityId, $name);

        if (!$this->entityId) {
            return;
        }

        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $entityFactory->get(BrandsEntity::class);
        $brand = $brandsEntity->get($this->entityId);
        if (empty($brand)) {
            return;
        }

        if ($this->name === null || $this->name === '') {
            $this->name = (string)($brand->name ?? '');
        }
        $this->annotation = $this->toPlainText($brand->annotation ?? '');
        $this->description = $this->toPlainText($brand->description ?? '');
        $this->additionalInfo = $this->buildContextBlock([
            'Brand name' => (string)$this->name,
            'Existing short description' => $this->annotation,
            'Existing description' => $this->description,
        ]);
    }

    public function getRequestText(string $field): string
    {
        $template = '';
        switch ($field) {
            case self::FIELD_META_TITLE:
                $template = $this->settings->get('ai_brand_title_template');
                break;
            case self::FIELD_META_DESCRIPTION:
                $template = $this->settings->get('ai_brand_meta_description_template');
                break;
            case self::FIELD_META_KEYWORDS:
                $template = $this->settings->get('ai_brand_keywords_template');
                break;
            case self::FIELD_ANNOTATION:
                $template = $this->settings->get('ai_brand_annotation_template');
                break;
            case self::FIELD_DESCRIPTION:
                $template = $this->settings->get('ai_brand_description_template');
        }
        return strtr((string)$template, [
            '{$brand}' => "\n'{$this->name}'\n",
            '{$annotation}' => $this->annotation,
            '{$description}' => $this->description,
        ]);
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
