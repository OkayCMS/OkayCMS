<?php


namespace Okay\Core\Modules;


use \Exception;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\Entity\Entity;

class EntityMigrator
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var SqlPresentor
     */
    private $sqlPresentor;

    public function __construct(Database $db, QueryFactory $queryFactory, SqlPresentor $sqlPresentor)
    {
        $this->queryFactory = $queryFactory;
        $this->db = $db;
        $this->sqlPresentor = $sqlPresentor;
    }

    /**
     * @param string $entityClassName
     * @param array $entityFields
     * @throws Exception
     * 
     * Метод создаёт таблицу в БД 
     * Метод принимает название класса сущности и массив объектов класса Okay\Core\Modules\EntityField
     */
    public function migrateEntityTable($entityClassName, array $entityFields)
    {
        /** @var Entity $entityClassName */
        $tableName = $entityClassName::getTable();
        if (empty($tableName)) {
            return;
        }

        $this->createTable($tableName, $entityFields);

        $langTableName = $entityClassName::getLangTable();
        if (empty($langTableName)) {
            return;
        }

        $langObject = $entityClassName::getLangObject();
        if (empty($langObject)) {
            throw new \Exception("Property {$langObject} cannot be empty for creating lang table");
        }

        $langObjectField = $langObject.'_id';
        $langEntityFields = [];
        /** @var EntityField $entityField */
        $langEntityFields[] = (new EntityField('lang_id'))->setTypeInt(11);
        $langEntityFields[] = (new EntityField($langObjectField))->setTypeInt(11);
        foreach ($entityFields as $entityField) {
            if ($entityField->isLangField()) {
                $langEntityFields[] = $entityField;
            }
        }

        $this->createTable($langTableName, $langEntityFields, $langObjectField);
    }
    
    public function migrateCustomTable($tableName, array $entityFields)
    {
        if (empty($tableName)) {
            return;
        }
        
        $tableName = '__' . preg_replace('~(__)?(.+)~', '$2', $tableName);
        $this->createTable($tableName, $entityFields);
    }

    private function createTable($tableName, array $entityFields, $langObjectField = null)
    {
        if (empty($tableName)) {
            return;
        }
        
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement($this->sqlPresentor->createTableQuery($tableName, $entityFields, $langObjectField));
        $this->db->query($sql);
    }
    
    public function migrateFieldSet($entityClassName, $entityFields)
    {
        foreach($entityFields as $entityField) {
            $this->migrateField($entityClassName, $entityField);
        }
    }

    /**
     * @param string $entityClassName
     * @param EntityField $entityField
     * @throws Exception
     * 
     * Добавление одного поля в базу, к уже существующей таблице
     */
    public function migrateField($entityClassName, EntityField $entityField)
    {
        if (!is_string($entityClassName) || !is_subclass_of($entityClassName, Entity::class)) {
            throw new Exception("\"$entityClassName\" must be class name subclass of \"" . Entity::class . "\"");
        }

        if ($this->isNewField($entityClassName, $entityField)) {
            $this->addFieldToDatabase($entityClassName, $entityField);
            return;
        }

        if ($this->fieldCouldBeUpdated($entityClassName, $entityField)) {
            $this->updateFieldInDatabase($entityClassName, $entityField);
            return;
        }
    }

    private function isNewField($entityClassName, EntityField $field)
    {
        /** @var Entity $entityClassName */
        if ($field->isLangField()) {
            $table = $entityClassName::getLangTable();
        } else {
            $table = $entityClassName::getTable();
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("SHOW COLUMNS FROM `$table`;");
        $this->db->query($sql);

        foreach ($this->db->results() as $currentField) {
            if ($currentField->Field === $field->getName()) {
                return false;
            }
        }

        return true;
    }

    private function fieldCouldBeUpdated($entityClassName, EntityField $field)
    {
        /** @var Entity $entityClassName */
        if ($field->isLangField()) {
            $table = $entityClassName::getLangTable();
        } else {
            $table = $entityClassName::getTable();
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("SHOW COLUMNS FROM `$table`;");
        $this->db->query($sql);

        foreach ($this->db->results() as $currentField) {
            if ($currentField->Field === $field->getName()) {
                $matchedField = $currentField;
                break;
            }
        }

        if (empty($matchedField)) {
            throw new \Exception('Field "'.$field->getName().'" not exists in table "'.$table.'"');
        }

        if (isset($currentField->Type) && $field->getType() != $matchedField->Type) {
            return true;
        }

        if (($field->isNullable() && $matchedField->Null == 'NO') ||
            ($field->isNotNullable() === false && $matchedField->Null == 'YES')) {
            return true;
        }

        if ($field->getDefault() != $matchedField->Default) {
            return true;
        }

        return false;
    }

    private function addFieldToDatabase($entityClassName, EntityField $field)
    {
        /** @var Entity $entityClassName */
        $sqlStatement = $this->sqlPresentor->addColumnQuery($entityClassName::getTable(), $field);

        if ($field->isLangField()) {
            $sqlStatement .= PHP_EOL;
            $sqlStatement .= $this->sqlPresentor->addColumnQuery($entityClassName::getLangTable(), $field);
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement($sqlStatement);
        return (bool) $this->db->query($sql);
    }

    private function updateFieldInDatabase($entityClassName, EntityField $field)
    {
        /** @var Entity $entityClassName */
        $sqlStatement = $this->sqlPresentor->changeColumnQuery($entityClassName::getTable(), $field);

        if ($field->isLangField()) {
            $sqlStatement .= PHP_EOL;
            $sqlStatement .= $this->sqlPresentor->changeColumnQuery($entityClassName::getLangTable(), $field);
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement($sqlStatement);
        return (bool) $this->db->query($sql);
    }
}