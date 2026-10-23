<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\CategoriesEntity;

class AiCategoryRequest extends AbstractAiRequest
{
    public const ENTITY_TYPE = 'category';
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
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        $category = $categoriesEntity->get($this->entityId);
        if (empty($category)) {
            return;
        }

        if ($this->name === null || $this->name === '') {
            $this->name = (string)($category->name ?? '');
        }
        $this->annotation = $this->toPlainText($category->annotation ?? '');
        $this->description = $this->toPlainText($category->description ?? '');
        $this->additionalInfo = $this->buildContextBlock([
            'Category name' => (string)$this->name,
            'Existing short description' => $this->annotation,
            'Existing description' => $this->description,
        ]);
    }

    public function getRequestText(string $field): string
    {
        $template = '';
        switch ($field) {
            case self::FIELD_META_TITLE:
                $template = $this->settings->get('ai_category_title_template');
                break;
            case self::FIELD_META_DESCRIPTION:
                $template = $this->settings->get('ai_category_meta_description_template');
                break;
            case self::FIELD_META_KEYWORDS:
                $template = $this->settings->get('ai_category_keywords_template');
                break;
            case self::FIELD_ANNOTATION:
                $template = $this->settings->get('ai_category_annotation_template');
                break;
            case self::FIELD_DESCRIPTION:
                $template = $this->settings->get('ai_category_description_template');
        }
        return strtr((string)$template, [
            '{$category}' => "\n'{$this->name}'\n",
            '{$annotation}' => $this->annotation,
            '{$description}' => $this->description,
        ]);
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
