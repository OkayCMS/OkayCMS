<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;
use Okay\Entities\SEOFilterPatternsEntity;
use Okay\Helpers\MetaRobotsHelper;

class CategoryMetadataHelper extends CommonMetadataHelper
{
 
    private $metaArray = [];
    private $seoFilterPattern;
    private $metaDelimiter = ', ';
    private $autoMeta;
    private $metaRobots;

    private $featuresPlusFeaturesIds = [];

    /** @var object */
    private $category;

    /** @var bool */
    private $isFilterPage;

    /** @var bool */
    private $isAllPages;

    /** @var int */
    private $currentPageNum;

    /** @var array */
    private $selectedFilters;

    /** @var string|null */
    private $keyword;

    public function setUp(
        $category,
        bool $isFilterPage = false,
        bool $isAllPages = false,
        int $currentPageNum = 1,
        array $selectedFilters = [],
        array $metaArray = [],
        ?string $keyword = null
    ): void {
        $this->category        = $category;
        $this->isFilterPage    = $isFilterPage;
        $this->isAllPages      = $isAllPages;
        $this->currentPageNum  = $currentPageNum;
        $this->selectedFilters = $selectedFilters;
        $this->metaArray       = $metaArray;
        $this->keyword         = $keyword;
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageH1 = parent::getH1Template();
        $seoFilterPatternH1 = !empty($seoFilterPattern->h1) ? $seoFilterPattern->h1 : null;
        $filterAutoMetaH1 = !empty($filterAutoMeta->h1) ? $filterAutoMeta->h1 : null;
        $categoryH1 = !empty($this->category->name_h1) ? $this->category->name_h1 : $this->category->name;

        $h1 = $this->matchPriorityH1($pageH1, $seoFilterPatternH1, $filterAutoMetaH1, $categoryH1);

        if ($this->keyword !== null) {
            $h1 .= " «{$this->keyword}»";
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    public function matchPriorityH1($pageH1, $seoFilterPatternH1, $filterAutoMetaH1, $categoryH1): string
    {
        if ($pageH1) {
            $h1 = $pageH1;
        } elseif (!empty($seoFilterPatternH1)) {
            $h1 = $seoFilterPatternH1;
        } elseif (!empty($filterAutoMetaH1)) {
            $h1 = $categoryH1 . ' ' . $filterAutoMetaH1;
        } else {
            $h1 = $categoryH1;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationTemplate(): string
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $seoFilterPatternAnnotation = $seoFilterPattern->annotation ?? null;
        $filterAutoMetaAnnotation = $filterAutoMeta->annotation ?? null;

        $annotation = $this->matchPriorityDescription(
            $this->currentPageNum,
            $this->isAllPages,
            '',
            $seoFilterPatternAnnotation,
            $filterAutoMetaAnnotation,
            $this->isFilterPage,
            $this->category->annotation
        );

        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageDescription = parent::getDescriptionTemplate();
        $seoFilterPatternDescription = !empty($seoFilterPattern->description) ? $seoFilterPattern->description : null;
        $filterAutoMetaDescription = !empty($filterAutoMeta->description) ? $filterAutoMeta->description : null;

        $description = $this->matchPriorityDescription(
            $this->currentPageNum,
            $this->isAllPages,
            $pageDescription,
            $seoFilterPatternDescription,
            $filterAutoMetaDescription,
            $this->isFilterPage,
            $this->category->description
        );

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    public function matchPriorityDescription(
        $currentPageNum,
        $isAllPages,
        $pageDescription,
        $seoFilterPatternDescription,
        $filterAutoMetaDescription,
        $isFilterPage,
        $categoryDescription
    ): string {
        if ((int)$currentPageNum > 1 || $isAllPages === true) {
            $description = '';
        } elseif ($pageDescription) {
            $description = $pageDescription;
        } elseif (!empty($seoFilterPatternDescription)) {
            $description = $seoFilterPatternDescription;
        /*} elseif (!empty($filterAutoMetaDescription)) {
            $description = $filterAutoMetaDescription;*/
        } elseif ($isFilterPage === false) {
            $description = (string)$categoryDescription;
        } else {
            $description = '';
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate(): string // todo проверить как отработают экстендеры если их навесить на этот метод (где юзается parent::getMetaTitle())
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageTitle = parent::getMetaTitleTemplate();
        $seoFilterPatternMetaTitle =  !empty($seoFilterPattern->meta_title) ? $seoFilterPattern->meta_title : null;
        $filterAutoMetaTitle = !empty($filterAutoMeta->meta_title) ? $filterAutoMeta->meta_title : null;

        $metaTitle = $this->matchPriorityMetaTitle($pageTitle, $seoFilterPatternMetaTitle, $filterAutoMetaTitle, $this->category->meta_title);

        // Добавим номер страницы к тайтлу
        if ((int)$this->currentPageNum > 1 && $this->isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $this->currentPageNum;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    public function matchPriorityMetaTitle($pageTitle, $seoFilterPatternMetaTitle, $filterAutoMetaTitle, $categoryMetaTitle): string
    {
        if ($pageTitle) {
            $metaTitle = $pageTitle;
        } elseif (!empty($seoFilterPatternMetaTitle)) {
            $metaTitle = $seoFilterPatternMetaTitle;
        } elseif (!empty($filterAutoMetaTitle)) {
            $metaTitle = $categoryMetaTitle . ' ' . $filterAutoMetaTitle;
        } else {
            $metaTitle = (string)$categoryMetaTitle;
        }

        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    public function getMetaKeywordsTemplate(): string
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageKeywords = parent::getMetaKeywordsTemplate();
        $seoFilterPatternMetaKeywords = !empty($seoFilterPattern->meta_keywords) ? $seoFilterPattern->meta_keywords : null;
        $filterAutoMetaMetaKeywords = !empty($filterAutoMeta->meta_keywords) ? $filterAutoMeta->meta_keywords : null;

        $metaKeywords = $this->matchPriorityMetaKeywords($pageKeywords, $seoFilterPatternMetaKeywords, $filterAutoMetaMetaKeywords, $this->category->meta_keywords);

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    public function matchPriorityMetaKeywords($pageKeywords, $seoFilterPatternMetaKeywords, $filterAutoMetaMetaKeywords, $categoryMetaKeywords): string
    {
        if ($pageKeywords) {
            $metaKeywords = $pageKeywords;
        } elseif (!empty($seoFilterPatternMetaKeywords)) {
            $metaKeywords = $seoFilterPatternMetaKeywords;
        } elseif (!empty($filterAutoMetaMetaKeywords)) {
            $metaKeywords = $categoryMetaKeywords . ' ' . $filterAutoMetaMetaKeywords;
        } else {
            $metaKeywords = (string)$categoryMetaKeywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate(): string
    {
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageMetaDescription = parent::getMetaDescriptionTemplate();
        $seoFilterPatternMetaDescription = !empty($seoFilterPattern->meta_description) ? $seoFilterPattern->meta_description : null;
        $filterAutoMetaMetaDescription = !empty($filterAutoMeta->meta_description) ? $filterAutoMeta->meta_description : null;

        $metaDescription = $this->matchPriorityMetaDescription($pageMetaDescription, $seoFilterPatternMetaDescription, $filterAutoMetaMetaDescription, $this->category->meta_description);

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    public function matchPriorityMetaDescription($pageMetaDescription, $seoFilterPatternMetaDescription, $filterAutoMetaMetaDescription, $categoryMetaDescription): string
    {
        if ($pageMetaDescription) {
            $metaDescription = $pageMetaDescription;
        } elseif (!empty($seoFilterPatternMetaDescription)) {
            $metaDescription = $seoFilterPatternMetaDescription;
        } elseif (!empty($filterAutoMetaMetaDescription)) {
            $metaDescription = $categoryMetaDescription . ' ' . $filterAutoMetaMetaDescription;
        } else {
            $metaDescription = (string)$categoryMetaDescription;
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
                'annotation' => '',
                'description' => '',
            ];

            if (!empty($this->metaArray)) {
                foreach ($this->metaArray as $type => $_meta_array) {
                    switch ($type) {
                        case 'brand': // no break
                        case 'filter':
                        {
                            $autoMeta['h1'] = $autoMeta['meta_title'] = $autoMeta['meta_keywords'] = $autoMeta['meta_description'] = $autoMeta['annotation'] = $autoMeta['description'] = implode($this->metaDelimiter, $_meta_array);
                            break;
                        }
                        case 'features_values':
                        {
                            foreach ($_meta_array as $f_id => $f_array) {
                                $autoMeta['h1'] .= (!empty($autoMeta['h1']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_title'] .= (!empty($autoMeta['meta_title']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_keywords'] .= (!empty($autoMeta['meta_keywords']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_description'] .= (!empty($autoMeta['meta_description']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['annotation'] .= (!empty($autoMeta['annotation']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['description'] .= (!empty($autoMeta['description']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                            }
                            break;
                        }
                    }
                }
            }
            $this->autoMeta = (object)$autoMeta;
        }

        return ExtenderFacade::execute(__METHOD__, $this->autoMeta, func_get_args());
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
            '{$category}' => ($this->category->name ? $this->category->name : ''),
            '{$category_h1}' => ($this->category->name_h1 ? $this->category->name_h1 : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];
        
        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->SL->getService(EntityFactory::class);
        
        if (!empty($this->selectedFilters)) {
            /** @var Languages $languages */
            $languages = $this->SL->getService(Languages::class);
          
            /** @var FeaturesAliasesValuesEntity $featuresAliasesValuesEntity */
            $featuresAliasesValuesEntity = $entityFactory->get(FeaturesAliasesValuesEntity::class);

            /** @var FeaturesValuesAliasesValuesEntity $featuresValuesAliasesValuesEntity */
            $featuresValuesAliasesValuesEntity = $entityFactory->get(FeaturesValuesAliasesValuesEntity::class);
            
            $featuresIds = array_keys($this->selectedFilters);

            $aliasesValuesFilter = [
                'lang_id' => $languages->getLangId(),
                'feature_id' => $featuresIds
            ];
            
            if (in_array(count($featuresIds), [1, 2])) {
                foreach ($this->selectedFilters as $sf) {
                    if(count($sf) == 1){
                        $aliasesValuesFilter['feature_value_id'][] = key($sf);
                    } else {
                        unset($aliasesValuesFilter['feature_value_id']);
                        break;
                    }
                }
            }

            //Если паттерн свойство+свойство
            if (!empty($aliasesValuesFilter['feature_value_id']) && count($aliasesValuesFilter['feature_value_id']) == 2) {
                
                $featureIdsInPatternSettingsOrder = [];
                //достаем порядок свойств в шаблонах в админке
                if ($seoFilterPattern = $this->getSeoFilterPattern()) {
                    $featureIdsInPatternSettingsOrder[] = $seoFilterPattern->feature_id;
                    $featureIdsInPatternSettingsOrder[] = $seoFilterPattern->second_feature_id;
                }

                $featuresAliasesForSelected = [];
                foreach ($featuresAliasesValuesEntity->find(array('feature_id'=>$featuresIds)) as $fv) {
                    $featuresAliasesForSelected[$fv->feature_id][$fv->variable] = $fv->value;
                }

                //получим для них все алиасы значения
                $aliasesValuesForSelected = [];
                foreach ($featuresValuesAliasesValuesEntity->find($aliasesValuesFilter) as $ov) {
                    $aliasesValuesForSelected[$ov->variable][$ov->feature_id] = $ov->value;
                }

                //необходимо заполнить алиасы свойств и их значений в правильном порядке
                if ($aliasesValuesForSelected) {
                    $counter = '';
                    foreach ($featureIdsInPatternSettingsOrder as $featureId) {
                        //заполняем алиасы свойств
                        foreach ($featuresAliasesForSelected[$featureId] as $type => $value) {
                            $this->parts['{$f_alias_'.$type.$counter.'}'] = $value ?? '';
                        }
                        //заполняем алиасы значений
                        foreach ($aliasesValuesForSelected as $type => $values) {
                            $this->parts['{$o_alias_'.$type.$counter.'}'] = $values[$featureId] ?? '';
                        }
                        $counter = '_2';
                    }
                }
            } else {
                //Если только одно значение одного свойства
                foreach ($featuresAliasesValuesEntity->find(array('feature_id'=>$featuresIds)) as $fv) {
                    $this->parts['{$f_alias_'.$fv->variable.'}'] = $fv->value;
                }

                //получим для него все алиасы значения
                foreach ($featuresValuesAliasesValuesEntity->find($aliasesValuesFilter) as $ov) {
                    $this->parts['{$o_alias_'.$ov->variable.'}'] = $ov->value;
                }
            }
        }

        if (!empty($this->metaArray['brand']) && count($this->metaArray['brand']) == 1) {
            $this->parts['{$brand}'] = reset($this->metaArray['brand']);
        }
        if (!empty($this->metaArray['features_values']) && count($this->metaArray['features_values']) == 1) {

            /** @var FeaturesEntity $featuresEntity */
            $featuresEntity = $entityFactory->get(FeaturesEntity::class);
            
            reset($this->metaArray['features_values']);
            $featureId = key($this->metaArray['features_values']);
            $feature = $featuresEntity->get((int)$featureId);

            $this->parts['{$feature_name}'] = $feature->name;
            $this->parts['{$feature_val}'] = implode(', ', reset($this->metaArray['features_values']));
        } elseif (!empty($this->metaArray['features_values']) && count($this->metaArray['features_values']) == 2) {

            if (empty($this->featuresPlusFeaturesIds) || count($this->featuresPlusFeaturesIds) !=2) {
                $this->featuresPlusFeaturesIds = array_keys($this->metaArray['features_values']);
            }

            /** @var FeaturesEntity $featuresEntity */
            $featuresEntity = $entityFactory->get(FeaturesEntity::class);
            $features = $featuresEntity->mappedBy('id')->find(['id'=>$this->featuresPlusFeaturesIds]);

            $this->parts['{$feature_name}'] = $features[$this->featuresPlusFeaturesIds[0]]->name;
            $this->parts['{$feature_val}'] = implode(', ', $this->metaArray['features_values'][$this->featuresPlusFeaturesIds[0]]);

            $this->parts['{$feature_name_2}'] = $features[$this->featuresPlusFeaturesIds[1]]->name;
            $this->parts['{$feature_val_2}'] = implode(', ', $this->metaArray['features_values'][$this->featuresPlusFeaturesIds[1]]);
        }
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }

    private function getSeoFilterPattern()
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
        
        if (empty($this->seoFilterPattern)) {
            $categoriesIdsForPattern = [0, $this->category->id];
            /** @var EntityFactory $entityFactory */
            $entityFactory = $this->SL->getService(EntityFactory::class);

            /** @var SEOFilterPatternsEntity $SEOFilterPatternsEntity */
            $SEOFilterPatternsEntity = $entityFactory->get(SEOFilterPatternsEntity::class);

            if (!empty($this->metaArray['brand']) && count($this->metaArray['brand']) == 1 && !empty($this->metaArray['features_values']) && count($this->metaArray['features_values']) == 1) {
                /** @var FeaturesEntity $featuresEntity */
                $featuresEntity = $entityFactory->get(FeaturesEntity::class);

                $seoFilterPatterns = [];
                foreach ($SEOFilterPatternsEntity->find(['category_id' => $categoriesIdsForPattern, 'type' => 'brand_feature']) as $p) {
                    $isDefaultKey = $p->category_id == 0 ? 'default_' : '';
                    $key = $isDefaultKey.'brand_feature' . (!empty($p->feature_id) ? '_' . $p->feature_id : '');
                    $seoFilterPatterns[$key] = $p;
                }

                reset($this->metaArray['features_values']);
                $featureId = key($this->metaArray['features_values']);
                $feature = $featuresEntity->get((int)$featureId);

                // Определяем какой шаблон брать по умолчанию, для категории + определенное свойство, или категории и любое свойство
                if (isset($seoFilterPatterns['default_brand_feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['default_brand_feature_' . $feature->id];
                } elseif (isset($seoFilterPatterns['default_brand_feature'])) {
                    $this->seoFilterPattern = $seoFilterPatterns['default_brand_feature'];
                }

                // Определяем какой шаблон брать, для категории + определенное свойство, или категории и любое свойство
                if (isset($seoFilterPatterns['brand_feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['brand_feature_' . $feature->id];
                } elseif (isset($seoFilterPatterns['brand_feature']) && !isset($seoFilterPatterns['default_brand_feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['brand_feature'];
                }

            } elseif (!empty($this->metaArray['brand']) && count($this->metaArray['brand']) == 1 && empty($this->metaArray['features_values'])) {
                $seoFilterPatterns = $SEOFilterPatternsEntity->mappedBy('category_id')->find(['category_id' => $categoriesIdsForPattern, 'type' => 'brand']);
                if (!empty($seoFilterPatterns[$this->category->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns[$this->category->id];
                } else {
                    $this->seoFilterPattern = reset($seoFilterPatterns);
                }

            } elseif (!empty($this->metaArray['features_values']) && count($this->metaArray['features_values']) == 1 && empty($this->metaArray['brand'])) {

                /** @var FeaturesEntity $featuresEntity */
                $featuresEntity = $entityFactory->get(FeaturesEntity::class);

                $seoFilterPatterns = [];
                foreach ($SEOFilterPatternsEntity->find(['category_id' => $categoriesIdsForPattern, 'type' => 'feature']) as $p) {
                    $isDefaultKey = $p->category_id == 0 ? 'default_' : '';
                    $key = $isDefaultKey.'feature' . (!empty($p->feature_id) ? '_' . $p->feature_id : '');
                    $seoFilterPatterns[$key] = $p;
                }

                reset($this->metaArray['features_values']);
                $featureId = key($this->metaArray['features_values']);
                $feature = $featuresEntity->get((int)$featureId);

                // Определяем какой шаблон брать по умолчанию, для категории + определенное свойство, или категории и любое свойство
                if (isset($seoFilterPatterns['default_feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['default_feature_' . $feature->id];
                } elseif (isset($seoFilterPatterns['default_feature'])) {
                    $this->seoFilterPattern = $seoFilterPatterns['default_feature'];
                }

                // Определяем какой шаблон брать, для категории + определенное свойство, или категории и любое свойство
                if (isset($seoFilterPatterns['feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['feature_' . $feature->id];
                } elseif (isset($seoFilterPatterns['feature']) && !isset($seoFilterPatterns['default_feature_' . $feature->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns['feature'];
                }

            } elseif (!empty($this->metaArray['features_values']) && count($this->metaArray['features_values']) == 2 && empty($this->metaArray['brand'])) {

                $featuresIds = [];
                foreach ($this->metaArray['features_values'] as $key=>$metaArrayFeatureValue) {
                    if (!in_array($key, $featuresIds)) {
                        $featuresIds[] = $key;
                    }
                }

                if (count($featuresIds)==2) {
                    /** @var FeaturesEntity $featuresEntity */
                    $featuresEntity = $entityFactory->get(FeaturesEntity::class);

                    $seoFilterPatterns = [];
                    foreach ($SEOFilterPatternsEntity->find(['category_id' => $categoriesIdsForPattern, 'type' => 'feature_feature']) as $p) {
                        $isDefaultKey = $p->category_id == 0 ? 'default_' : '';
                        $key = $isDefaultKey.'feature_feature' . (!empty($p->feature_id) ? '_' . $p->feature_id : '').(!empty($p->second_feature_id) ? '_' . $p->second_feature_id : '');
                        $seoFilterPatterns[$key] = $p;
                    }

                    $features = $featuresEntity->find(['id'=>$featuresIds]);
                    $this->featuresPlusFeaturesIds = [];
                    // Определяем какой шаблон брать по умолчанию, а так же порядок id свойств, для категории + определенное свойство + определенное свойство,
                    // либо +определенное свойство +любое, либо любое+
                    $isDefaultSpecificFeature = false;
                    if (isset($seoFilterPatterns['default_feature_feature_' . $features[0]->id.'_'.$features[1]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['default_feature_feature_' . $features[0]->id.'_'.$features[1]->id];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                        $isDefaultSpecificFeature = true;
                    } elseif (isset($seoFilterPatterns['default_feature_feature_' . $features[1]->id.'_'.$features[0]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['default_feature_feature_' . $features[1]->id.'_'.$features[0]->id];
                        $this->featuresPlusFeaturesIds = [$features[1]->id, $features[0]->id];
                        $isDefaultSpecificFeature = true;
                    } elseif (isset($seoFilterPatterns['default_feature_feature_' . $features[0]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['default_feature_feature_' . $features[0]->id];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                        $isDefaultSpecificFeature = true;
                    } elseif (isset($seoFilterPatterns['default_feature_feature_' . $features[1]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['default_feature_feature_' . $features[1]->id];
                        $this->featuresPlusFeaturesIds = [$features[1]->id, $features[0]->id];
                        $isDefaultSpecificFeature = true;
                    } elseif (isset($seoFilterPatterns['default_feature_feature'])) {
                        $this->seoFilterPattern = $seoFilterPatterns['default_feature_feature'];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                    }

                    // Определяем какой шаблон брать, а так же порядок id свойств, для категории + определенное свойство + определенное свойство,
                    // либо +определенное свойство +любое, либо любое+любое
                    if (isset($seoFilterPatterns['feature_feature_' . $features[0]->id.'_'.$features[1]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['feature_feature_' . $features[0]->id.'_'.$features[1]->id];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                    } elseif (isset($seoFilterPatterns['feature_feature_' . $features[1]->id.'_'.$features[0]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['feature_feature_' . $features[1]->id.'_'.$features[0]->id];
                        $this->featuresPlusFeaturesIds = [$features[1]->id, $features[0]->id];
                    } elseif (isset($seoFilterPatterns['feature_feature_' . $features[0]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['feature_feature_' . $features[0]->id];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                    } elseif (isset($seoFilterPatterns['feature_feature_' . $features[1]->id])) {
                        $this->seoFilterPattern = $seoFilterPatterns['feature_feature_' . $features[1]->id];
                        $this->featuresPlusFeaturesIds = [$features[1]->id, $features[0]->id];
                    //по умолчанию приоритет за конкретным свойством
                    } elseif (isset($seoFilterPatterns['feature_feature']) && !$isDefaultSpecificFeature) {
                        $this->seoFilterPattern = $seoFilterPatterns['feature_feature'];
                        $this->featuresPlusFeaturesIds = [$features[0]->id, $features[1]->id];
                    }
                }
            }
        }
        
        return $this->seoFilterPattern;
    }
}