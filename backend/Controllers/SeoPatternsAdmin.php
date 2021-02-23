<?php


namespace Okay\Admin\Controllers;


use Okay\Core\BackendTranslations;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;

class SeoPatternsAdmin extends IndexAdmin
{

    public function fetch(
        CategoriesEntity $categoriesEntity,
        FeaturesEntity $featuresEntity,
        BackendTranslations $backendTranslationsCore
    ) {
        $this->design->setTemplatesDir('backend/design/html');
        $this->design->setCompiledDir('backend/design/compiled');

        if ($this->request->post("ajax")){
            /*Получение категории*/
            if ($this->request->post("action") == "get") {
                $result = new \stdClass();

                if ($this->request->post('template_type') == 'default') {
                    $defaultProductsSeoPattern = (object)$this->settings->default_products_seo_pattern;
                    $defaultProductsSeoPattern->name = $backendTranslationsCore->getTranslation('seo_patterns_all_categories');
                    $this->design->assign("category", $defaultProductsSeoPattern);
                    $result->success = true;
                } else {
                    $category = $categoriesEntity->get($this->request->post("category_id", "integer"));
                    if (!empty($category->id)) {
                        $this->design->assign('features', $featuresEntity->find(array('category_id' => $category->id)));
                        $this->design->assign("category", $category);
                        $result->success = true;
                    } else {
                        $result->success = false;
                    }
                }
                
                $result->tpl = $this->design->fetch("seo_patterns_ajax.tpl");
                $this->response->setContent(json_encode($result), RESPONSE_JSON);
                return;
            }

            /*Обновление шаблона данных категории*/
            if ($this->request->post("action") == "set") {
                $result = new \stdClass();
                if ($this->request->post('template_type') == 'default') {
                    $defaultProductsSeoPattern['auto_meta_title']    = $this->request->post('auto_meta_title');
                    $defaultProductsSeoPattern['auto_meta_keywords'] = $this->request->post('auto_meta_keywords');
                    $defaultProductsSeoPattern['auto_meta_desc']     = $this->request->post('auto_meta_desc');
                    $defaultProductsSeoPattern['auto_description']   = $this->request->post('auto_description');
                    $defaultProductsSeoPattern['auto_h1']            = $this->request->post('auto_h1');

                    $this->settings->update('default_products_seo_pattern', $defaultProductsSeoPattern);
                    $defaultProductsSeoPattern = (object)$defaultProductsSeoPattern;
                    $defaultProductsSeoPattern->name = $backendTranslationsCore->getTranslation('seo_patterns_all_categories');
                    $this->design->assign("category", $defaultProductsSeoPattern);
                    $result->success = true;
                } else {

                    $categoryId = $this->request->post("category_id", "integer");
                    if ($category = $categoriesEntity->get($categoryId)) {
                        $categoryToUpdate = new \stdClass();
                        $categoryToUpdate->auto_meta_title      = $this->request->post('auto_meta_title');
                        $categoryToUpdate->auto_meta_keywords   = $this->request->post('auto_meta_keywords');
                        $categoryToUpdate->auto_meta_desc       = $this->request->post('auto_meta_desc');
                        $categoryToUpdate->auto_description     = $this->request->post('auto_description');
                        $categoryToUpdate->auto_h1              = $this->request->post('auto_h1');
                        
                        $categoriesEntity->update($category->id, $categoryToUpdate);
                        $category = $categoriesEntity->get($categoryId);
                        $this->design->assign('features', $featuresEntity->find(array('category_id' => $category->id)));
                        $this->design->assign("category", $category);
                        $result->success = true;
                    } else {
                        $result->success = false;
                    }
                }

                $result->tpl = $this->design->fetch("seo_patterns_ajax.tpl");
                $this->response->setContent(json_encode($result), RESPONSE_JSON);
                return;

            }
        }

        $categories = $categoriesEntity->getCategoriesTree();
        $this->design->assign('categories', $categories);

        $this->response->setContent($this->design->fetch('seo_patterns.tpl'));
    }
}
