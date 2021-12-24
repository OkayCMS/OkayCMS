<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\MetaRobotsHelper;

class BrandMetadataHelper extends CommonMetadataHelper
{
 
    private $metaArray = [];
    private $metaDelimiter = ', ';
    private $autoMeta;
    private $metaRobots;

    /** @var object */
    private $brand;

    /** @var bool */
    private $isFilterPage;

    /** @var bool */
    private $isAllPages;

    /** @var int */
    private $currentPageNum;

    /** @var string|null */
    private $keyword;

    public function setUp(
        $brand,
        bool $isFilterPage = false,
        bool $isAllPages = false,
        int $currentPageNum = 1,
        array $metaArray = [],
        ?string $keyword = null
    ): void {
        $this->brand          = $brand;
        $this->isFilterPage   = $isFilterPage;
        $this->isAllPages     = $isAllPages;
        $this->currentPageNum = $currentPageNum;
        $this->metaArray      = $metaArray;
        $this->keyword        = $keyword;
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } elseif (!empty($filterAutoMeta->h1)) {
            $h1 = $this->brand->name . ' ' . $filterAutoMeta->h1;
        } elseif (!empty($this->brand->name_h1)) {
            $h1 = (string)$this->brand->name_h1;
        } else {
            $h1 = (string)$this->brand->name;
        }

        if ($this->keyword !== null) {
            $h1 .= " «{$this->keyword}»";
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationTemplate(): string
    {
        if ((int)$this->currentPageNum > 1 || $this->isAllPages === true) {
            $annotation = '';
        } elseif ($pageAnnotation = parent::getAnnotationTemplate()) {
            $annotation = $pageAnnotation;
        } elseif ($this->isFilterPage === false) {
            $annotation = (string)$this->brand->annotation;
        } else {
            $annotation = '';
        }

        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        if ((int)$this->currentPageNum > 1 || $this->isAllPages === true) {
            $description = '';
        } elseif ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } elseif ($this->isFilterPage === false) {
            $description = (string)$this->brand->description;
        } else {
            $description = '';
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate(): string
    {
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } elseif (!empty($filterAutoMeta->meta_title)) {
            $metaTitle = $this->brand->meta_title . ' ' . $filterAutoMeta->meta_title;
        } else {
            $metaTitle = (string)$this->brand->meta_title;
        }

        // Добавим номер страницы к тайтлу
        if ((int)$this->currentPageNum > 1 && $this->isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $this->currentPageNum;
        }

        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywordsTemplate(): string
    {
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } elseif (!empty($filterAutoMeta->meta_keywords)) {
            $metaKeywords = $this->brand->meta_keywords . ' ' . $filterAutoMeta->meta_keywords;
        } else {
            $metaKeywords = (string)$this->brand->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate(): string
    {
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } elseif (!empty($filterAutoMeta->meta_description)) {
            $metaDescription = $this->brand->meta_description . ' ' . $filterAutoMeta->meta_description;
        } else {
            $metaDescription = (string)$this->brand->meta_description;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    private function getFilterAutoMeta()
    {

        if (empty($this->metaRobots)) {
            /** @var MetaRobotsHelper $metaRobotsHelper */
            $metaRobotsHelper = $this->SL->getService(MetaRobotsHelper::class);

            $currentPage = $this->metaArray['page'] ?? null;
            $currentBrands = $this->metaArray['brand'] ?? [];
            $currentOtherFilters = $this->metaArray['filter'] ?? [];
            $filterFeatures = $this->metaArray['features_values'] ?? [];

            $this->metaRobots = $metaRobotsHelper->getCatalogRobots($currentPage, $currentOtherFilters, $filterFeatures, $currentBrands);
        }

        if ($this->metaRobots == ROBOTS_NOINDEX_FOLLOW || $this->metaRobots == ROBOTS_NOINDEX_NOFOLLOW) {
            return false;
        }
        
        if (empty($this->autoMeta)) {
            
            $autoMeta = [
                'h1' => '',
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => '',
            ];

            if (!empty($this->metaArray)) {
                foreach ($this->metaArray as $type => $_meta_array) {
                    switch ($type) {
                        case 'brand': // no break
                        case 'filter':
                        {
                            $autoMeta['h1'] = $autoMeta['meta_title'] = $autoMeta['meta_keywords'] = $autoMeta['meta_description'] = $autoMeta['description'] = implode($this->metaDelimiter, $_meta_array);
                            break;
                        }
                    }
                }
            }
            $this->autoMeta = (object)$autoMeta;
        }

        return $this->autoMeta;
    }
    
    /**
     * @inheritDoc
     */
    protected function getParts(): array
    {
        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }
        
        $this->parts = [
            '{$brand}' => ($this->brand->name ? $this->brand->name : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }
}