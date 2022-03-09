<?php


namespace Okay\Admin\Controllers;


use Aura\SqlQuery\QueryFactory;
use Okay\Core\Languages;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesAliasesEntity;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;

class FeaturesAliasesAdmin extends IndexAdmin
{
    public function fetch(
        FeaturesAliasesEntity $featuresAliasesEntity,
        FeaturesAliasesValuesEntity $featuresAliasesValuesEntity,
        FeaturesValuesAliasesValuesEntity $featuresValuesAliasesValuesEntity,
        FeaturesEntity $featuresEntity,
        FeaturesValuesEntity $featuresValuesEntity,
        Languages $languagesCore,
        QueryFactory $queryFactory
    ) {

        $this->design->setTemplatesDir('backend/design/html');
        $this->design->setCompiledDir('backend/design/compiled');

        if ($this->request->post("ajax")){
            $result = new \stdClass();

            /*Обновление шаблона данных категории*/
            if ($this->request->post("action") == "set") {
                $result->success = false;

                if ($featureId = $this->request->post("feature_id", "integer")) {
                    $feature = $featuresEntity->get($featureId);
                }

                if (!empty($feature) && !empty($feature->id)) {
                    $result->success = true;
                    $featuresAliases = [];
                    $featuresAliasesIds = [];
                    $featuresAliasesValues = [];
                    if ($this->request->post('features_aliases')) {
                        foreach ($this->request->post('features_aliases') as $n=>$fa) {
                            foreach ($fa as $i=>$a) {
                                if (empty($featuresAliases[$i])) {
                                    $featuresAliases[$i] = new \stdClass;
                                }
                                $featuresAliases[$i]->$n = $a;
                            }
                        }
                    }

                    if ($this->request->post('feature_aliases_value')) {
                        foreach ($this->request->post('feature_aliases_value') as $n=>$fav) {
                            foreach ($fav as $i=>$av) {
                                if (empty($featuresAliasesValues[$i])) {
                                    $featuresAliasesValues[$i] = new \stdClass;
                                }
                                $featuresAliasesValues[$i]->$n = $av;
                            }
                        }
                    }

                    foreach ($featuresAliases as $k=>$featuresAlias) {
                        if ($featuresAlias->name) {
                            if (!empty($featuresAlias->id)) {
                                $featuresAliasesEntity->update($featuresAlias->id, $featuresAlias);
                            } else {
                                unset($featuresAlias->id);
                                $featuresAlias->id = $featuresAliasesEntity->add($featuresAlias);
                            }
                        }

                        // Добавим все значения для алиасов которые нам запостили
                        if (isset($featuresAliasesValues[$k]) && $featuresAlias->id) {
                            $aliasValue = $featuresAliasesValues[$k];
                            $aliasValue->feature_id = $feature->id;
                            $aliasValue->feature_alias_id = $featuresAlias->id;

                            if (!empty($aliasValue->id)) {
                                $featuresAliasesValuesEntity->update($aliasValue->id, $aliasValue);
                            } else {
                                unset($aliasValue->id);
                                $featuresAliasesValuesEntity->add($aliasValue);
                            }
                        }

                        $featuresAlias = $featuresAliasesEntity->get((int)$featuresAlias->id);
                        if (!empty($featuresAlias->id)) {
                            $featuresAliasesIds[] = $featuresAlias->id;
                        }
                    }

                    $currentFeaturesAliases = $featuresAliasesEntity->find();
                    foreach ($currentFeaturesAliases as $currentFeaturesAlias) {
                        if (!in_array($currentFeaturesAlias->id, $featuresAliasesIds)) {
                            $currentFeatureAliasValues = $featuresAliasesValuesEntity->find(['feature_alias_id'=>$currentFeaturesAlias->id]);
                            foreach ($currentFeatureAliasValues as $cv) {
                                $featuresAliasesValuesEntity->delete($cv->id);
                            }
                            $featuresAliasesEntity->delete($currentFeaturesAlias->id);
                        }
                    }

                    asort($featuresAliasesIds);
                    $i = 0;
                    foreach($featuresAliasesIds as $featuresAlias_id) {
                        $featuresAliasesEntity->update($featuresAliasesIds[$i], array('position'=>$featuresAlias_id));
                        $i++;
                    }

                    $featuresAliases = $featuresAliasesEntity->mappedBy('id')->find();

                    foreach ($featuresAliasesValuesEntity->find(['feature_id'=>$feature->id]) as $fv) {
                        $featuresAliases[$fv->feature_alias_id]->value = $fv;
                    }

                    if ($optionsAliases = $this->request->post('options_aliases')) {
                        // Удалим все алиасы значений свойств для текущего языка
                        $delete = $queryFactory->newDelete();
                        $delete->from(FeaturesValuesAliasesValuesEntity::getTable())
                            ->where('feature_id=:feature_id AND lang_id=:lang_id')
                            ->where('feature_value_id IN (:feature_value_ids)')
                            ->bindValues([
                                'feature_id' => $feature->id,
                                'lang_id' => $languagesCore->getLangId(),
                                'feature_value_ids' => array_keys($optionsAliases)
                            ]);
                        $this->db->query($delete);

                        $featuresValues = $featuresValuesEntity->mappedBy('id')->find(['feature_id'=>$feature->id]);

                        foreach ($optionsAliases as $featureValueId=>$values) {
                            foreach ($values as $feature_alias_id=>$value) {
                                if (!empty($value) && isset($featuresAliases[$feature_alias_id]) && isset($featuresValues[$featureValueId])) {
                                    $optionAlias = new \stdClass;
                                    $optionAlias->feature_value_id = $featureValueId;
                                    $optionAlias->value    = $value;
                                    $optionAlias->lang_id  = $languagesCore->getLangId();
                                    $optionAlias->feature_id       = $feature->id;
                                    $optionAlias->feature_alias_id = $feature_alias_id;
                                    $featuresValuesAliasesValuesEntity->add($optionAlias);
                                    $featuresValues[$featureValueId]->aliases[$feature_alias_id] = $optionAlias;
                                }
                            }
                        }
                    }
                }
            }

            $featuresAliases = $featuresAliasesEntity->mappedBy('id')->find();

            if ($featureId = $this->request->post("feature_id", "integer")) {
                $feature = $featuresEntity->get($featureId);
            } else {
                $feature = new \stdClass();
            }

            if (!empty($feature) && !empty($feature->id)) {
                $filter = [
                    'feature_id' => $feature->id
                ];

                $featuresValuesCount = $featuresValuesEntity->count($filter);

                $page = $this->request->get('page', 'int', 1);

                if ($this->request->get('page') === 'all') {
                    $filter['page'] = 1;
                    $filter['limit'] = $featuresValuesCount;
                } else {
                    $filter['page'] = $page;
                    $filter['limit'] = 10;
                }

                $this->design->assign('pages_count',  ceil($featuresValuesCount/$filter['limit']));
                $this->design->assign('current_page', $filter['page']);

                $featuresValues = $featuresValuesEntity->mappedBy('id')
                    ->find($filter);

                if (!empty($featuresValuesIds = array_keys($featuresValues))) {
                    foreach ($featuresValuesAliasesValuesEntity->find(['feature_value_id' => $featuresValuesIds]) as $oa) {
                        $featuresValues[$oa->feature_value_id]->aliases[$oa->feature_alias_id] = $oa;
                    }
                }

                $this->design->assign('features_values', $featuresValues);

                foreach ($featuresAliasesValuesEntity->find(['feature_id'=>$feature->id]) as $fv) {
                    $featuresAliases[$fv->feature_alias_id]->value = $fv;
                }
            }

            $this->design->assign('feature', $feature);
            $this->design->assign('features_aliases', $featuresAliases);

            $result->feature_aliases_tpl = $this->design->fetch("features_aliases_ajax.tpl");
            $result->feature_aliases_values_tpl = $this->design->fetch("features_aliases_values_ajax.tpl");
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            return;
        }

        $featuresCount = $featuresEntity->count();
        $features = $featuresEntity->find(['limit'=>$featuresCount]);

        $this->design->assign('features', $features);

        $this->response->setContent($this->design->fetch('features_aliases.tpl'));
    }
}