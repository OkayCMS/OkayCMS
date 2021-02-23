<?php


namespace Okay\Core\Modules;


class SqlPresentor
{
    public function addColumnQuery($tableName, EntityField $field)
    {
        $sql = "ALTER TABLE `{$tableName}` ADD COLUMN {$this->fieldSql($field)};";

        if ($indexes = $field->getIndexes()) {
            $indexFields = [];
            foreach ($indexes as $indexType => $index) {
                $length = $index['length'];
                $indexName = $field->getName();
                $indexFields[] = "`{$field->getName()}`" . ($length !== null ? "({$length})" : "");
                // Если указали дополнительные поля для составного индекса, добавим их
                if (!empty($index['fields'])) {
                    foreach ($index['fields'] as $compositeIndexField) {
                        /** @var EntityField $compositeIndexField*/
                        $indexName .= '_' . $compositeIndexField->getName();
                        $indexFields[] = "`{$compositeIndexField->getName()}`";
                    }
                }

                $fullIndexFields = implode(', ', $indexFields);
                $sql .= "ALTER TABLE `{$tableName}` ADD {$indexType} `{$indexName}` ({$fullIndexFields});";
            }
        }
        
        return $sql;
    }

    public function changeColumnQuery($tableName, EntityField $field)
    {
        return "ALTER TABLE `{$tableName}` CHANGE `{$field->getName()}` {$this->fieldSql($field)};";
    }

    public function createTableQuery($tableName, array $entityFields, $langObjectField = null)
    {
        $primaryKeyExists = false;

        $sql  = "CREATE TABLE {$tableName} (";

        /** @var EntityField $entityField */
        foreach ($entityFields as $entityField) {
            $sql .= $this->fieldSql($entityField) . ",";
            
            if ($indexes = $entityField->getIndexes()) {
                $indexFields = [];
                foreach ($indexes as $indexType => $index) {
                    $length = $index['length'];
                    $indexName = $entityField->getName();
                    //var_dump($length);
                    $indexFields[] = "`{$entityField->getName()}`" 
                        . ($length !== null ? "({$length})" : "");
                    // Если указали дополнительные поля для составного индекса, добавим их
                    if (!empty($index['fields'])) {
                        foreach ($index['fields'] as $compositeIndexField) {
                            /** @var EntityField $compositeIndexField*/
                            $indexName .= '_' . $compositeIndexField->getName();
                            $indexFields[] = "`{$compositeIndexField->getName()}`";
                        }
                    }
                    
                    $fullIndexFields = implode(', ', $indexFields);
                    $sql .= ($indexType !== EntityField::INDEX ? $indexType : "") . " KEY `{$indexName}` ({$fullIndexFields}),";
                }
            }

            if ($entityField->isPrimaryKey() && $primaryKeyExists === false) {
                $sql .= "PRIMARY KEY (`{$entityField->getName()}`),";
                $primaryKeyExists = true;
            } elseif($entityField->isPrimaryKey()) {
                throw new \Exception("Table can use only one primary key");
            }
        }

        if ($langObjectField !== null) {
            $sql .= "UNIQUE KEY `lang_id_{$langObjectField}` (`lang_id`, `{$langObjectField}`),";
        }
        
        $sql = substr($sql, 0, -1);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $sql;
    }

    private function fieldSql(EntityField $field)
    {
        $nullSql    = $field->isNullable() && !$field->isPrimaryKey() ? ' NULL' : ' NOT NULL';
        $defaultSql = "";
        if ($field->getDefault() !== null) {
            // если как значение по умолчанию указали функцию, её не берём в кавычки
            if (preg_match('~.+?\(.*?\)~', $field->getDefault())) {
                $defaultSql = " DEFAULT {$field->getDefault()}";
            } else {
                $defaultSql = " DEFAULT '{$field->getDefault()}'";
            }
        }
        $autoIncrementSql = $field->isAutoIncrement() ? " AUTO_INCREMENT " : "";

        return "`{$field->getName()}` {$field->getType()} {$nullSql} {$defaultSql} {$autoIncrementSql}";
    }
}