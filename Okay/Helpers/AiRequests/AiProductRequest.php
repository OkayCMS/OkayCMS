<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ProductsEntity;

class AiProductRequest extends AbstractAiRequest
{
    public const ENTITY_TYPE = 'product';
    public const FIELD_META_TITLE = 'meta_title';
    public const FIELD_META_DESCRIPTION = 'meta_description';
    public const FIELD_META_KEYWORDS= 'meta_keywords';
    public const FIELD_ANNOTATION = 'annotation';
    public const FIELD_DESCRIPTION = 'description';

    private string $brandName = '';
    private string $categoryName = '';
    private string $annotation = '';
    private string $description = '';
    private string $featuresText = '';
    private string $additionalInfo = '';

    public function __construct(?int $entityId, ?string $name)
    {
        parent::__construct($entityId, $name);

        if (!$this->entityId) {
            return;
        }
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $entityFactory->get(ProductsEntity::class);
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $entityFactory->get(FeaturesEntity::class);
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $entityFactory->get(BrandsEntity::class);
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);

        $product = $productsEntity->get($this->entityId);
        if (!empty($product)) {
            if ($this->name === null || $this->name === '') {
                $this->name = (string)($product->name ?? '');
            }
            $this->annotation = $this->toPlainText($product->annotation ?? '');
            $this->description = $this->toPlainText($product->description ?? '');

            if (!empty($product->brand_id)) {
                $brand = $brandsEntity->get((int)$product->brand_id);
                if (!empty($brand->name)) {
                    $this->brandName = (string)$brand->name;
                }
            }

            $categoryId = null;
            if (!empty($product->main_category_id)) {
                $categoryId = (int)$product->main_category_id;
            } else {
                $productCategories = $categoriesEntity->getProductCategories($this->entityId);
                if (!empty($productCategories)) {
                    $first = reset($productCategories);
                    $categoryId = (int)$first->category_id;
                }
            }
            if ($categoryId) {
                $category = $categoriesEntity->get($categoryId);
                if (!empty($category->name)) {
                    $this->categoryName = (string)$category->name;
                }
            }
        }

        $featuresValues = [];
        foreach ($featuresValuesEntity->find(['product_id' => $this->entityId]) as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }
        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresIds)) {
            $featureLines = [];
            foreach ($featuresEntity->find(['id' => $featuresIds]) as $f) {
                if (!empty($featuresValues[$f->id])) {
                    $values = [];
                    foreach ($featuresValues[$f->id] as $fv) {
                        $values[] = $fv->value;
                    }
                    $featureLines[] = sprintf(
                        '%s: %s',
                        $f->name,
                        implode(', ', $values)
                    );
                }
            }
            $this->featuresText = implode("\n", $featureLines);
        }

        $this->additionalInfo = $this->buildContextBlock([
            'Product name' => (string)$this->name,
            'Brand' => $this->brandName,
            'Category' => $this->categoryName,
            'Existing short description' => $this->annotation,
            'Existing description' => $this->description,
            'Verified specifications' => $this->featuresText,
        ]);
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
        return strtr((string)$template, [
            '{$product}' => "\n'{$this->name}'\n",
            '{$brand}' => $this->brandName,
            '{$category}' => $this->categoryName,
            '{$annotation}' => $this->annotation,
            '{$description}' => $this->description,
            '{$features}' => $this->featuresText,
        ]);
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
