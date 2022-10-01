<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\Modules\Extender\ExtenderFacade;

class ProductMetadataHelper extends CommonMetadataHelper
{
    /** @var object */
    private $product;

    /** @var object|null */
    private $category;

    /** @var object|null */
    private $brand;

    public function setUp(object $product, ?object $category = null, ?object $brand = null): void
    {
        $this->product  = $product;
        $this->category = $category;
        $this->brand    = $brand;
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        $h1 = (string)$this->product->name;
        if ($data = $this->getCategoryField('auto_h1')) {
            $h1 = $data;
        } elseif(!empty($defaultProductsSeoPattern->auto_h1)) {
            $h1 = $defaultProductsSeoPattern->auto_h1;
        } elseif (count($this->product->variants) == 1 && !empty($this->product->variant->name)) {
            $h1 .= ' ' . $this->product->variant->name;
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationTemplate(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        $annotation = (string)$this->product->annotation;
        if (empty($annotation)) {
            if ($data = $this->getCategoryField('auto_annotation')) {
                $annotation = $data;
            } elseif (!empty($defaultProductsSeoPattern->auto_annotation)) {
                $annotation = $defaultProductsSeoPattern->auto_annotation;
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        $description = (string)$this->product->description;
        if (empty($description)) {
            if ($data = $this->getCategoryField('auto_description')) {
                $description = $data;
            } elseif (!empty($defaultProductsSeoPattern->auto_description)) {
                $description = $defaultProductsSeoPattern->auto_description;
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        if ($data = $this->getCategoryField('auto_meta_title')) {
            $metaTitle = $data;
        } elseif (!empty($defaultProductsSeoPattern->auto_meta_title)) {
            $metaTitle = $defaultProductsSeoPattern->auto_meta_title;
        } else {
            $metaTitle = (string)$this->product->meta_title;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywordsTemplate(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        if ($data = $this->getCategoryField('auto_meta_keywords')) {
            $metaKeywords = $data;
        } elseif (!empty($defaultProductsSeoPattern->auto_meta_keywords)) {
            $metaKeywords = $defaultProductsSeoPattern->auto_meta_keywords;
        } else {
            $metaKeywords = (string)$this->product->meta_keywords;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate(): string
    {
        $defaultProductsSeoPattern = (object)$this->settings->get('default_products_seo_pattern');

        if ($data = $this->getCategoryField('auto_meta_desc')) {
            $metaDescription = $data;
        } elseif (!empty($defaultProductsSeoPattern->auto_meta_desc)) {
            $metaDescription = $defaultProductsSeoPattern->auto_meta_desc;
        } else {
            $metaDescription = (string)$this->product->meta_description;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    /**
     * Метод возвращает массив переменных и их значений, который учавствуют в формировании метаданных
     * @return array
     */
    protected function getParts(): array
    {
        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }
        
        $currency = $this->mainHelper->getCurrentCurrency();

        $this->parts = [
            '{$brand}'         => ($this->brand ? $this->brand->name : ''),
            '{$product}'       => ($this->product ? $this->product->name : ''),
            '{$price}'         => ($this->product->variant->price != null ? $this->money->convert($this->product->variant->price, $currency->id, false) . ' ' . $currency->sign : ''),
            '{$compare_price}' => ($this->product->variant->compare_price != null ? $this->money->convert($this->product->variant->compare_price, $currency->id, false) . ' ' . $currency->sign : ''),
            '{$sku}'           => ($this->product->variant->sku != null ? $this->product->variant->sku : ''),
            '{$sitename}'      => ($this->settings->get('site_name') ? $this->settings->get('site_name') : '')
        ];

        if ($this->category) {
            $this->parts['{$category}'] = ($this->category->name ? $this->category->name : '');
            $this->parts['{$category_h1}'] = ($this->category->name_h1 ? $this->category->name_h1 : '');

            if (!empty($this->product->features)) {
                foreach ($this->product->features as $feature) {
                    if ($feature->auto_name_id) {
                        $this->parts['{$' . $feature->auto_name_id . '}'] = $feature->name;
                    }
                    if ($feature->auto_value_id) {
                        $this->parts['{$' . $feature->auto_value_id . '}'] = $feature->stingify_values;
                    }
                }
            }
        }
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }

    private function getCategoryField($fieldName)
    {
        if (!empty($this->category)) {
            foreach (array_reverse($this->category->path) as $c) {
                if (!empty($c->{$fieldName})) {
                    return $c->{$fieldName};
                }
            }
        }
        return false;
    }
}