<?php


namespace Okay\Core\Entity;


trait order
{

    /**
     * @param string $order
     * @param array $additionalData просто кастомный массив данных, который может понадобиться
     * @return $this
     */
    final public function order($order = null, array $additionalData = [])
    {
        $this->select->resetOrderBy();

        $orderFields = $this->autoOrder($order);

        // Если не определили сортировку, тогда применим по умолчанию
        if (empty($orderFields)) {
            $orderFields = $this->getDefaultOrderFields();
        }
        
        $orderFields = $this->customOrder($order, $orderFields, $additionalData);

        if (!empty($orderFields)) {

            $langFields = $this->getLangFields();
            $fields = $this->getFields();
            foreach ($orderFields as $k => $orderField) {
                $orderFieldName = preg_replace('~^([\w\.\-]+)?.*$~', '$1', $orderField);

                // Если алиас не задали ранее, добавим его сейчас
                if (strpos($orderFieldName, '.') === false) {
                    $tableAlias = $this->getTableAlias();

                    // Если сортируют по полю, которое объявлено как мультиленговое, установим соответствующий алиас
                    if (in_array($orderFieldName, $langFields)) {
                        $tableAlias = $this->lang->getLangAlias(
                            $this->getTableAlias()
                        );
                    }
                    
                    // добавляем алиас только если поле находится в списке $fields или $langFields сущности
                    if (in_array($orderFieldName, $langFields) || in_array($orderFieldName, $fields)) {
                        $orderField = $tableAlias . '.' . $orderField;
                    }
                    
                    $orderFields[$k] = $orderField;
                }
            }

            $this->select->orderBy($orderFields);
        }

        return $this;
    }

    /**
     * @param string $order
     * @return array
     * "Магическая" сортировка, если $order = 'field' или 'field_asc' или 'field_desc' то применяется сортировка по указанному полю в указанном направлении
     */
    private function autoOrder($order = null)
    {
        $orderFields = [];
        $langFields = $this->getLangFields();
        $fields = $this->getFields();
        $allFields = array_merge($langFields, $fields);

        // todo иногда $order это массив, разобраться
        if (preg_match('~^([\w\.\-]+)(:?_(asc|desc))?$~iU', $order, $matches)
            && in_array(strtolower($matches[1]), $allFields)) {
            $field = $matches[1];
            $rotation = !empty($matches[3]) ? strtoupper($matches[3]) : 'ASC';
            $orderFields = [$field . ' ' . $rotation];
        }
        
        return $orderFields;
    }
}