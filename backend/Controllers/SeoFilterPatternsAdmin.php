<?php


namespace Okay\Admin\Controllers;


use Okay\Core\BackendTranslations;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesAliasesEntity;
use Okay\Entities\SEOFilterPatternsEntity;

class SeoFilterPatternsAdmin extends IndexAdmin
{

    public function fetch(
        SEOFilterPatternsEntity $SEOFilterPatternsEntity,
        FeaturesEntity $featuresEntity,
        FeaturesAliasesEntity $featuresAliasesEntity,
        CategoriesEntity $categoriesEntity,
        BackendTranslations $backendTranslationsCore
    ) {
        $this->design->setTemplatesDir('backend/design/html');
        $this->design->setCompiledDir('backend/design/compiled');

        if ($this->request->post("ajax")){

            $result = new \stdClass();
            if ($this->request->post("action") == "get_features") {

                $filterFeatures['in_filter'] = 1;
                if ($this->request->post("template_type") == 'category') {
                    $filterFeatures['category_id'] = $this->request->post("category_id", "integer");
                }

                $result->features = $featuresEntity->find($filterFeatures);
                $result->success = true;
            }
            /*Получение SEO шаблонов*/
            if ($this->request->post("action") == "get") {

                $isDefaultCategory = false;
                if ($this->request->post("template_type") == 'default') {
                    $category = new \stdClass();
                    $category->id = 0;
                    $category->name = $backendTranslationsCore->getTranslation('seo_patterns_all_categories');
                    $isDefaultCategory = true;
                } else {
                    $category = $categoriesEntity->get($this->request->post("category_id", "integer"));
                }

                if (!empty($category->id) || $isDefaultCategory) {
                    $featuresIds = [];
                    $patterns = [];
                    $features = [];
                    foreach ($SEOFilterPatternsEntity->find(['category_id'=>$category->id]) as $p) {
                        $patterns[$p->id] = $p;
                        if ($p->feature_id) {
                            $featuresIds[] = $p->feature_id;
                        }
                        if ($p->second_feature_id) {
                            $featuresIds[] = $p->second_feature_id;
                        }
                    }

                    if (!empty($featuresIds)) {
                        $featuresIds = array_unique($featuresIds);
                        foreach ($featuresEntity->find(['id' => $featuresIds]) as $f) {
                            $features[$f->id] = $f;
                        }

                        foreach ($patterns as $p) {
                            if ($p->feature_id && isset($features[$p->feature_id])) {
                                $p->feature = $features[$p->feature_id];
                            }
                            if ($p->second_feature_id && isset($features[$p->second_feature_id])) {
                                $p->second_feature = $features[$p->second_feature_id];
                            }
                        }
                    }
                    $this->design->assign('patterns', $patterns);
                    $this->design->assign("categories_for_copy", $categoriesEntity->getCategoriesTree());
                    $this->design->assign("category", $category);
                    $featuresAliases = $featuresAliasesEntity->find();
                    $this->design->assign("features_aliases", $featuresAliases);
                    $result->success = true;
                } else {
                    $result->success = false;
                }
                $result->tpl = $this->design->fetch("seo_filter_patterns_ajax.tpl");
            }

            /*Копирование SEO шаблонов*/
            if ($this->request->post("action") == "copy_patterns_from_category") {

                $isDefaultToCopyCategory = false;
                $result->success   = false;

                $categoryFromCopyId = $this->request->post("category_from_copy_id", "integer");

                if ($this->request->post("template_type") == 'default') {
                    $categoryToCopyId = 0;
                    $isDefaultToCopyCategory = true;
                } else {
                    $categoryToCopyId = $this->request->post("category_to_copy_id");
                }

                if ($categoryFromCopyId && ($categoryToCopyId || $isDefaultToCopyCategory)) {

                    // Собираем ключи, для понимания есть ли в базе такой ключ для категории в которую копируем
                    $indexesValuesTo = [];
                    foreach ($SEOFilterPatternsEntity->find(['category_id'=>$categoryToCopyId]) as $p) {
                        $indexesValuesTo[$p->id] = "{$categoryToCopyId}_{$p->type}_{$p->feature_id}_{$p->second_feature_id}";
                    }

                    $patternsFromCopy    = [];
                    foreach ($SEOFilterPatternsEntity->find(['category_id'=>$categoryFromCopyId]) as $p) {
                        if ((array_search("{$categoryToCopyId}_{$p->type}_{$p->feature_id}_{$p->second_feature_id}", $indexesValuesTo)) === false) {
                            $patternsFromCopy[$p->id] = $p;
                        }
                    }

                    foreach ($patternsFromCopy as $patternFromCopy) {
                        $patternFromCopy->id = null;
                        $patternFromCopy->category_id = $categoryToCopyId;
                        $SEOFilterPatternsEntity->add($patternFromCopy);

                    }
                    $result->success = true;
                }
            }

            /*Обновление шаблона данных категории*/
            if ($this->request->post("action") == "set") {

                $result->success = true;

                $isDefaultCategory = false;
                if ($this->request->post("template_type") == 'default') {
                    $category = new \stdClass();
                    $category->id = 0;
                    $category->name = $backendTranslationsCore->getTranslation('seo_patterns_all_categories');
                    $isDefaultCategory = true;
                } else {
                    $category = $categoriesEntity->get($this->request->post("category_id", "integer"));
                }

                if (!empty($category->id) || $isDefaultCategory) {
                    $seoFilterPatterns = $this->request->post('seo_filter_patterns');
                    $patterns = [];
                    $patternsIds = [];
                    if (is_array($seoFilterPatterns)) {

                        foreach ($this->request->post('seo_filter_patterns') as $n=>$pa) {
                            foreach ($pa as $i=>$p) {
                                if (empty($patterns[$i])) {
                                    $patterns[$i] = new \stdClass;
                                }
                                $patterns[$i]->$n = $p;
                                if ($n == 'id') {
                                    $patternsIds[] = $p;
                                }
                            }
                        }
                    }
                    // Удалим паттерны которые не запостили
                    $currentPatterns = $SEOFilterPatternsEntity->find(['category_id' => $category->id]);
                    foreach ($currentPatterns as $current_pattern) {
                        if (!in_array($current_pattern->id, $patternsIds)) {
                            $SEOFilterPatternsEntity->delete($current_pattern->id);
                        }
                    }

                    if ($patterns) {
                        foreach ($patterns as $pattern) {
                            if (!$pattern->feature_id) {
                                $pattern->feature_id = null;
                            }
                            if (!$pattern->second_feature_id) {
                                $pattern->second_feature_id = null;
                            }
                            if (!empty($pattern->id)) {
                                $SEOFilterPatternsEntity->update($pattern->id, $pattern);
                            } else {
                                $pattern->category_id = $category->id;
                                $pattern->id = $SEOFilterPatternsEntity->add($pattern);
                            }
                        }
                    }

                    $featuresIds = [];
                    $patterns = [];
                    $features = [];
                    foreach ($SEOFilterPatternsEntity->find(['category_id'=>$category->id]) as $p) {
                        $patterns[$p->id] = $p;
                        if ($p->feature_id) {
                            $featuresIds[] = $p->feature_id;
                        }
                        if ($p->second_feature_id) {
                            $featuresIds[] = $p->second_feature_id;
                        }
                    }

                    $featuresIds = array_unique($featuresIds);
                    foreach ($featuresEntity->find(['id'=>$featuresIds]) as $f) {
                        $features[$f->id] = $f;
                    }

                    foreach ($patterns as $p) {
                        if ($p->feature_id && isset($features[$p->feature_id])) {
                            $p->feature = $features[$p->feature_id];
                        }
                        if ($p->second_feature_id && isset($features[$p->second_feature_id])) {
                            $p->second_feature = $features[$p->second_feature_id];
                        }
                    }
                    $this->design->assign('patterns', $patterns);
                    $this->design->assign("categories_for_copy", $categoriesEntity->getCategoriesTree());
                    $this->design->assign("category", $category);
                    $featuresAliases = $featuresAliasesEntity->find();
                    $this->design->assign("features_aliases", $featuresAliases);
                    $result->tpl = $this->design->fetch("seo_filter_patterns_ajax.tpl");
                }
            }

            if ($result) {
                $this->response->setContent(json_encode($result), RESPONSE_JSON);
                return;
            }
        }

        $categories = $categoriesEntity->getCategoriesTree();
        $this->design->assign('categories', $categories);

        $this->response->setContent($this->design->fetch('seo_filter_patterns.tpl'));
    }
}
