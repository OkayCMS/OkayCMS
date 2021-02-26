<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;
use Okay\Entities\SEOFilterPatternsEntity;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetaRobotsHelper;

class CategoryMetadataHelper extends CommonMetadataHelper
{
 
    private $metaArray = [];
    private $seoFilterPattern;
    private $metaDelimiter = ', ';
    private $autoMeta;
    private $metaRobots;

    private $featuresPlusFeaturesIds = [];

    /**
     * @inheritDoc
     */
    public function getH1Template()
    {
        $category = $this->design->getVar('category');
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageH1 = parent::getH1Template();
        $seoFilterPatternH1 = !empty($seoFilterPattern->h1) ? $seoFilterPattern->h1 : null;
        $filterAutoMetaH1 = !empty($filterAutoMeta->h1) ? $filterAutoMeta->h1 : null;
        $categoryH1 = !empty($category->name_h1) ? $category->name_h1 : $category->name;

        $h1 = $this->matchPriorityH1($pageH1, $seoFilterPatternH1, $filterAutoMetaH1, $categoryH1);

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    public function matchPriorityH1($pageH1, $seoFilterPatternH1, $filterAutoMetaH1, $categoryH1)
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
    public function getDescriptionTemplate()
    {
        $category = $this->design->getVar('category');
        $isFilterPage = $this->design->getVar('is_filter_page');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageDescription = parent::getDescriptionTemplate();
        $seoFilterPatternDescription = !empty($seoFilterPattern->description) ? $seoFilterPattern->description : null;
        $filterAutoMetaDescription = !empty($filterAutoMeta->description) ? $filterAutoMeta->description : null;

        $description = $this->matchPriorityDescription($currentPageNum, $isAllPages, $pageDescription, $seoFilterPatternDescription, $filterAutoMetaDescription, $isFilterPage, $category->description);

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    public function matchPriorityDescription($currentPageNum, $isAllPages, $pageDescription, $seoFilterPatternDescription, $filterAutoMetaDescription, $isFilterPage, $categoryDescription)
    {
        if ((int)$currentPageNum > 1 || $isAllPages === true) {
            $description = '';
        } elseif ($pageDescription) {
            $description = $pageDescription;
        } elseif (!empty($seoFilterPatternDescription)) {
            $description = $seoFilterPatternDescription;
        /*} elseif (!empty($filterAutoMetaDescription)) {
            $description = $filterAutoMetaDescription;*/
        } elseif ($isFilterPage === false) {
            $description = $categoryDescription;
        } else {
            $description = '';
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate() // todo проверить как отработают экстендеры если их навесить на этот метод (где юзается parent::getMetaTitle())
    {
        $category = $this->design->getVar('category');
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

        $pageTitle = parent::getMetaTitleTemplate();
        $seoFilterPatternMetaTitle =  !empty($seoFilterPattern->meta_title) ? $seoFilterPattern->meta_title : null;
        $filterAutoMetaTitle = !empty($filterAutoMeta->meta_title) ? $filterAutoMeta->meta_title : null;

        $metaTitle = $this->matchPriorityMetaTitle($pageTitle, $seoFilterPatternMetaTitle, $filterAutoMetaTitle, $category->meta_title);

        // Добавим номер страницы к тайтлу
        if ((int)$currentPageNum > 1 && $isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $currentPageNum;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    public function matchPriorityMetaTitle($pageTitle, $seoFilterPatternMetaTitle, $filterAutoMetaTitle, $categoryMetaTitle)
    {
        if ($pageTitle) {
            $metaTitle = $pageTitle;
        } elseif (!empty($seoFilterPatternMetaTitle)) {
            $metaTitle = $seoFilterPatternMetaTitle;
        } elseif (!empty($filterAutoMetaTitle)) {
            $metaTitle = $categoryMetaTitle . ' ' . $filterAutoMetaTitle;
        } else {
            $metaTitle = $categoryMetaTitle;
        }

        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    public function getMetaKeywordsTemplate()
    {
        $category = $this->design->getVar('category');
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageKeywords = parent::getMetaKeywordsTemplate();
        $seoFilterPatternMetaKeywords = !empty($seoFilterPattern->meta_keywords) ? $seoFilterPattern->meta_keywords : null;
        $filterAutoMetaMetaKeywords = !empty($filterAutoMeta->meta_keywords) ? $filterAutoMeta->meta_keywords : null;

        $metaKeywords = $this->matchPriorityMetaKeywords($pageKeywords, $seoFilterPatternMetaKeywords, $filterAutoMetaMetaKeywords, $category->meta_keywords);

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    public function matchPriorityMetaKeywords($pageKeywords, $seoFilterPatternMetaKeywords, $filterAutoMetaMetaKeywords, $categoryMetaKeywords)
    {
        if ($pageKeywords) {
            $metaKeywords = $pageKeywords;
        } elseif (!empty($seoFilterPatternMetaKeywords)) {
            $metaKeywords = $seoFilterPatternMetaKeywords;
        } elseif (!empty($filterAutoMetaMetaKeywords)) {
            $metaKeywords = $categoryMetaKeywords . ' ' . $filterAutoMetaMetaKeywords;
        } else {
            $metaKeywords = $categoryMetaKeywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate()
    {
        $category = $this->design->getVar('category');
        $seoFilterPattern = $this->getSeoFilterPattern();
        $filterAutoMeta = $this->getFilterAutoMeta();

        $pageMetaDescription = parent::getMetaDescriptionTemplate();
        $seoFilterPatternMetaDescription = !empty($seoFilterPattern->meta_description) ? $seoFilterPattern->meta_description : null;
        $filterAutoMetaMetaDescription = !empty($filterAutoMeta->meta_description) ? $filterAutoMeta->meta_description : null;

        $metaDescription = $this->matchPriorityMetaDescription($pageMetaDescription, $seoFilterPatternMetaDescription, $filterAutoMetaMetaDescription, $category->meta_description);

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    public function matchPriorityMetaDescription($pageMetaDescription, $seoFilterPatternMetaDescription, $filterAutoMetaMetaDescription, $categoryMetaDescription)
    {
        if ($pageMetaDescription) {
            $metaDescription = $pageMetaDescription;
        } elseif (!empty($seoFilterPatternMetaDescription)) {
            $metaDescription = $seoFilterPatternMetaDescription;
        } elseif (!empty($filterAutoMetaMetaDescription)) {
            $metaDescription = $categoryMetaDescription . ' ' . $filterAutoMetaMetaDescription;
        } else {
            $metaDescription = $categoryMetaDescription;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    private function getFilterAutoMeta()
    {
        
        if (empty($this->metaRobots)) {
            /** @var MetaRobotsHelper $metaRobotsHelper */
            $metaRobotsHelper = $this->SL->getService(MetaRobotsHelper::class);

            $metaArray = $this->getMetaArray();

            $currentPage = isset($metaArray['page']) ? $metaArray['page'] : null;
            $currentBrands = isset($metaArray['brand']) ? $metaArray['brand'] : [];
            $currentOtherFilters = isset($metaArray['filter']) ? $metaArray['filter'] : [];
            $filterFeatures = isset($metaArray['features_values']) ? $metaArray['features_values'] : [];

            $this->metaRobots = $metaRobotsHelper->getCategoryRobots($currentPage, $currentOtherFilters, $filterFeatures, $currentBrands);
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

            $metaArray = $this->getMetaArray();
            if (!empty($metaArray)) {
                foreach ($metaArray as $type => $_meta_array) {
                    switch ($type) {
                        case 'brand': // no break
                        case 'filter':
                        {
                            $autoMeta['h1'] = $autoMeta['meta_title'] = $autoMeta['meta_keywords'] = $autoMeta['meta_description'] = $autoMeta['description'] = implode($this->metaDelimiter, $_meta_array);
                            break;
                        }
                        case 'features_values':
                        {
                            foreach ($_meta_array as $f_id => $f_array) {
                                $autoMeta['h1'] .= (!empty($autoMeta['h1']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_title'] .= (!empty($autoMeta['meta_title']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_keywords'] .= (!empty($autoMeta['meta_keywords']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
                                $autoMeta['meta_description'] .= (!empty($autoMeta['meta_description']) ? $this->metaDelimiter : '') . implode($this->metaDelimiter, $f_array);
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
    protected function getParts()
    {

        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }
        
        $category = $this->design->getVar('category');
        
        $this->parts = [
            '{$category}' => ($category->name ? $category->name : ''),
            '{$category_h1}' => ($category->name_h1 ? $category->name_h1 : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];

        $selectedFilters = $this->design->getVar('selected_filters');
        
        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->SL->getService(EntityFactory::class);
        
        if (!empty($selectedFilters)) {
            /** @var FeaturesAliasesValuesEntity $featuresAliasesValuesEntity */
            $featuresAliasesValuesEntity = $entityFactory->get(FeaturesAliasesValuesEntity::class);

            /** @var FeaturesValuesAliasesValuesEntity $featuresValuesAliasesValuesEntity */
            $featuresValuesAliasesValuesEntity = $entityFactory->get(FeaturesValuesAliasesValuesEntity::class);
            
            $featuresIds = array_keys($selectedFilters);
            
            foreach ($featuresAliasesValuesEntity->find(array('feature_id'=>$featuresIds)) as $fv) {
                $this->parts['{$f_alias_'.$fv->variable.'}'] = $fv->value;
            }

            $aliasesValuesFilter['feature_id'] = $featuresIds;
            // Если только одно значение одного свойства, получим для него все алиасы значения
            if (count($featuresIds) == 1 && (count($translits = reset($selectedFilters))) == 1) {
                $aliasesValuesFilter['translit'] = reset($translits);
            }
            foreach ($featuresValuesAliasesValuesEntity->find($aliasesValuesFilter) as $ov) {
                $this->parts['{$o_alias_'.$ov->variable.'}'] = $ov->value;
            }
        }

        $metaArray = $this->getMetaArray();

        if (!empty($metaArray['brand']) && count($metaArray['brand']) == 1) {
            $this->parts['{$brand}'] = reset($metaArray['brand']);
        }
        if (!empty($metaArray['features_values']) && count($metaArray['features_values']) == 1) {

            /** @var FeaturesEntity $featuresEntity */
            $featuresEntity = $entityFactory->get(FeaturesEntity::class);
            
            reset($metaArray['features_values']);
            $featureId = key($metaArray['features_values']);
            $feature = $featuresEntity->get((int)$featureId);

            $this->parts['{$feature_name}'] = $feature->name;
            $this->parts['{$feature_val}'] = implode(', ', reset($metaArray['features_values']));
        } elseif (!empty($metaArray['features_values']) && count($metaArray['features_values']) == 2) {

            if (empty($this->featuresPlusFeaturesIds) || count($this->featuresPlusFeaturesIds) !=2) {
                $this->featuresPlusFeaturesIds = array_keys($metaArray['features_values']);
            }

            /** @var FeaturesEntity $featuresEntity */
            $featuresEntity = $entityFactory->get(FeaturesEntity::class);
            $features = $featuresEntity->mappedBy('id')->find(['id'=>$this->featuresPlusFeaturesIds]);

            $this->parts['{$feature_name}'] = $features[$this->featuresPlusFeaturesIds[0]]->name;
            $this->parts['{$feature_val}'] = implode(', ', $metaArray['features_values'][$this->featuresPlusFeaturesIds[0]]);

            $this->parts['{$feature_name_2}'] = $features[$this->featuresPlusFeaturesIds[1]]->name;
            $this->parts['{$feature_val_2}'] = implode(', ', $metaArray['features_values'][$this->featuresPlusFeaturesIds[1]]);
        }
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }

    private function getSeoFilterPattern()
    {
        
        if (empty($this->metaRobots)) {
            /** @var MetaRobotsHelper $metaRobotsHelper */
            $metaRobotsHelper = $this->SL->getService(MetaRobotsHelper::class);

            $metaArray = $this->getMetaArray();

            $currentPage = isset($metaArray['page']) ? $metaArray['page'] : null;
            $currentBrands = isset($metaArray['brand']) ? $metaArray['brand'] : [];
            $currentOtherFilters = isset($metaArray['filter']) ? $metaArray['filter'] : [];
            $filterFeatures = isset($metaArray['features_values']) ? $metaArray['features_values'] : [];

            $this->metaRobots = $metaRobotsHelper->getCategoryRobots($currentPage, $currentOtherFilters, $filterFeatures, $currentBrands);
        }
        
        if ($this->metaRobots == ROBOTS_NOINDEX_FOLLOW || $this->metaRobots == ROBOTS_NOINDEX_NOFOLLOW) {
            return false;
        }
        
        if (empty($this->seoFilterPattern)) {
            $category = $this->design->getVar('category');
            $categoriesIdsForPattern = [0, $category->id];
            /** @var EntityFactory $entityFactory */
            $entityFactory = $this->SL->getService(EntityFactory::class);

            /** @var SEOFilterPatternsEntity $SEOFilterPatternsEntity */
            $SEOFilterPatternsEntity = $entityFactory->get(SEOFilterPatternsEntity::class);

            if (!empty($metaArray['brand']) && count($metaArray['brand']) == 1 && !empty($metaArray['features_values']) && count($metaArray['features_values']) == 1) {
                /** @var FeaturesEntity $featuresEntity */
                $featuresEntity = $entityFactory->get(FeaturesEntity::class);

                $seoFilterPatterns = [];
                foreach ($SEOFilterPatternsEntity->find(['category_id' => $categoriesIdsForPattern, 'type' => 'brand_feature']) as $p) {
                    $isDefaultKey = $p->category_id == 0 ? 'default_' : '';
                    $key = $isDefaultKey.'brand_feature' . (!empty($p->feature_id) ? '_' . $p->feature_id : '');
                    $seoFilterPatterns[$key] = $p;
                }

                reset($metaArray['features_values']);
                $featureId = key($metaArray['features_values']);
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

            } elseif (!empty($metaArray['brand']) && count($metaArray['brand']) == 1 && empty($metaArray['features_values'])) {
                $seoFilterPatterns = $SEOFilterPatternsEntity->mappedBy('category_id')->find(['category_id' => $categoriesIdsForPattern, 'type' => 'brand']);
                if (!empty($seoFilterPatterns[$category->id])) {
                    $this->seoFilterPattern = $seoFilterPatterns[$category->id];
                } else {
                    $this->seoFilterPattern = reset($seoFilterPatterns);
                }

            } elseif (!empty($metaArray['features_values']) && count($metaArray['features_values']) == 1 && empty($metaArray['brand'])) {

                /** @var FeaturesEntity $featuresEntity */
                $featuresEntity = $entityFactory->get(FeaturesEntity::class);

                $seoFilterPatterns = [];
                foreach ($SEOFilterPatternsEntity->find(['category_id' => $categoriesIdsForPattern, 'type' => 'feature']) as $p) {
                    $isDefaultKey = $p->category_id == 0 ? 'default_' : '';
                    $key = $isDefaultKey.'feature' . (!empty($p->feature_id) ? '_' . $p->feature_id : '');
                    $seoFilterPatterns[$key] = $p;
                }

                reset($metaArray['features_values']);
                $featureId = key($metaArray['features_values']);
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

            } elseif (!empty($metaArray['features_values']) && count($metaArray['features_values']) == 2 && empty($metaArray['brand'])) {

                $featuresIds = [];
                foreach ($metaArray['features_values'] as $key=>$metaArrayFeatureValue) {
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

    private function getMetaArray()
    {
        if (empty($this->metaArray)) {
            /** @var FilterHelper $filterHelper */
            $filterHelper = $this->SL->getService(FilterHelper::class);
            $this->metaArray = $filterHelper->getMetaArray();
        }
        return $this->metaArray;
    }
    
}