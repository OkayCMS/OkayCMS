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
            if ($this->request->post("action") == "get") {
                $result = new \stdClass();
                $result->success = false;

                $featuresAliases = $featuresAliasesEntity->mappedBy('id')->find();

                $feature = new \stdClass();
                if ($featureId = $this->request->post("feature_id", "integer")) {
                    $feature = $featuresEntity->get($featureId);
                }

                if (!empty($feature) && !empty($feature->id)) {

                    $featuresValues = [];
                    foreach ($featuresValuesEntity->find(['feature_id'=>$feature->id]) as $fv) {
                        $featuresValues[$fv->translit] = $fv;
                    }

                    foreach ($featuresValuesAliasesValuesEntity->find(['feature_id'=>$feature->id]) as $oa) {
                        $featuresValues[$oa->translit]->aliases[$oa->feature_alias_id] = $oa;
                    }
                    $this->design->assign('features_values', $featuresValues);

                    foreach ($featuresAliasesValuesEntity->find(['feature_id'=>$feature->id]) as $fv) {
                        $featuresAliases[$fv->feature_alias_id]->value = $fv;
                    }
                    $result->success = true;
                }

                $this->design->assign('feature', $feature);
                $this->design->assign('features_aliases', $featuresAliases);

                $result->feature_aliases_tpl = $this->design->fetch("features_aliases_ajax.tpl");
                $result->feature_aliases_values_tpl = $this->design->fetch("features_aliases_values_ajax.tpl");
                $this->response->setContent(json_encode($result), RESPONSE_JSON);
                return;

            }

            /*Обновление шаблона данных категории*/
            if ($this->request->post("action") == "set") {

                $result = new \stdClass();
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
                    
                    $this->design->assign('features_aliases', $featuresAliases);

                    // Удалим все алиасы значений свойств для текущего языка
                    if (!empty($feature->id)) {
                        $delete = $queryFactory->newDelete();
                        $delete->from(FeaturesValuesAliasesValuesEntity::getTable())
                            ->where('feature_id=:feature_id AND lang_id=:lang_id')
                            ->bindValues([
                                'feature_id' => $feature->id,
                                'lang_id' => $languagesCore->getLangId(),
                            ]);
                        $this->db->query($delete);
                    }

                    $featuresValues = [];
                    foreach ($featuresValuesEntity->find(['feature_id'=>$feature->id]) as $fv) {
                        $featuresValues[$fv->translit] = $fv;
                    }
                    $this->design->assign('features_values', $featuresValues);

                    if ($feature->id && $this->request->post('options_aliases')) {
                        foreach ($this->request->post('options_aliases') as $o_translit=>$values) {
                            foreach ($values as $feature_alias_id=>$value) {
                                if (!empty($value) && isset($featuresAliases[$feature_alias_id]) && isset($featuresValues[$o_translit])) {
                                    $optionAlias = new \stdClass;
                                    $optionAlias->translit = $o_translit;
                                    $optionAlias->value    = $value;
                                    $optionAlias->lang_id  = $languagesCore->getLangId();
                                    $optionAlias->feature_id       = $feature->id;
                                    $optionAlias->feature_alias_id = $feature_alias_id;
                                    $featuresValuesAliasesValuesEntity->add($optionAlias);
                                    $featuresValues[$o_translit]->aliases[$feature_alias_id] = $optionAlias;
                                }
                            }
                        }
                    }
                    $this->design->assign('feature', $feature);
                }

                $result->feature_aliases_tpl = $this->design->fetch("features_aliases_ajax.tpl");
                $result->feature_aliases_values_tpl = $this->design->fetch("features_aliases_values_ajax.tpl");
                $this->response->setContent(json_encode($result), RESPONSE_JSON);
                return;

            }
        }

        $featuresCount = $featuresEntity->count();
        $features = $featuresEntity->find(['limit'=>$featuresCount]);

        $this->design->assign('features', $features);

        $this->response->setContent($this->design->fetch('features_aliases.tpl'));
    }
}
