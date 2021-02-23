<?php


namespace Okay\Core\Entity;


trait lang
{

    /**
     * @param array $langParams
     * @return array
     * Возвращает массив колонок сущности с алиасами.
     * Если через setSelectFields() установили кастомный список колонок, метод вернет их в формате неассоциативного массива
     */
    final public function getAllFields($langParams = [])
    {
        return array_values($this->getAllFieldsKeyLabel($langParams));
    }

    /**
     * @param array $langParams
     * @return array
     * Возвращает массив колонок сущности с алиасами.
     * Если через setSelectFields() установили кастомный список колонок, метод вернет их в формате ассоциативного массива
     * где ключем будут имена полей без алиасов таблиц
     */
    final public function getAllFieldsKeyLabel($langParams = [])
    {
        if (!empty($this->selectFields)) {

            $fields = [];
            $langFields = [];
            $customFields = [];
            foreach ($this->selectFields as $f) {
                preg_match('~^([\w`\.]*\.)?`?(.+?)`?(:? as (.+))?$~i', $f, $matches);
                $fieldName = $matches[2];

                if (in_array($fieldName, $this->getLangFields())) {
                    $langFields[] = $f;
                } elseif (in_array($fieldName, $this->getFields())) {
                    $fields[] = $f;
                } else {
                    $customFields[] = $f;
                }
            }

            $fields = $this->getFieldsWithAlias($fields, $langParams);
            $langFields = $this->getFieldsWithAlias($langFields, $langParams);

            $fields = array_merge($fields, $customFields);

        } else {
            $fields = $this->getFieldsWithAlias($this->getFields(), $langParams);
            $langFields = $this->getFieldsWithAlias($this->getLangFields(), $langParams);
            $additional = $this->getAdditionalFields();
            $fields = array_merge($fields, $additional);
        }

        return array_merge($fields, $langFields);
    }

    /**
     * @return array
     * Метод возвращает список полей сущности без алиасв таблиц указываемых перед именем поля (Пример: l.name => name)
     */
    final public function getAllFieldsWithoutAlias()
    {
        $allFields = $this->getAllFields();

        foreach ($allFields as &$f) {
            preg_match('~^([\w`\.]*\.)?`?(.+?)`?(:? as (.+))?$~i', $f, $matches);
            $f = isset($matches[4]) ? $matches[4] : $matches[2];
        }
        unset($f);

        return $allFields;
    }

    protected function getDescription($object, $clear = true)
    {
        return $this->lang->getDescription(
            $object,
            $this->getLangFields(),
            $clear
        );
    }

    protected function actionDescription($objectId, $description, $updateLangId = null)
    {
        $this->lang->actionDescription(
            $objectId,
            $description,
            $this->getLangFields(),
            $this->getLangObject(),
            $this->getLangTable(),
            $updateLangId
        );
    }

    /**
     * @param array $fields
     * @param array $params
     * @return array
     * Метод добавляет алиасы для основных колонок сущности (если их еще не задали)
     */
    protected function getFieldsWithAlias(array $fields, $params = [])
    {
        $result = [];

        if (is_array($fields)) {
            foreach ($fields as $field) {

                if (in_array($field, $this->getLangFields())) {
                    $tableAlias = $this->lang->getLangAlias($this->getTableAlias(), $params);
                } else {
                    $tableAlias = $this->getTableAlias();
                }

                $label = $this->removeAlias($field);
                if ($this->hasTableAlias($field)) {
                    $result[$label] = $field;
                } else {
                    $result[$label] = $tableAlias . '.' . $field;
                }
            }
        }

        return $result;
    }

    protected function removeAlias($field)
    {
        if (!preg_match('~^\w{1,5}\..+$~', $field)) {
            return $field;
        }

        return explode('.', $field)[1];
    }

    private function hasTableAlias($field) 
    {
        if (preg_match('~^\w{1,5}\..+$~', $field)) {
            return true;
        }

        return false;
    }

}