<?php


namespace Okay\Core\Modules;


use \Exception;

class EntityField
{
    
    const TYPE_VARCHAR = 'varchar';
    const TYPE_INT     = 'int';
    const TYPE_TINYINT = 'tinyint';
    const TYPE_FLOAT   = 'float';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_TEXT    = 'text';
    const TYPE_ENUM    = 'enum';
    const TYPE_MEDIUMTEXT = 'mediumtext';
    const TYPE_LONGTEXT   = 'longtext';
    const TYPE_DATETIME   = 'datetime';
    const TYPE_TIMESTAMP  = 'timestamp';
    const INDEX = 'INDEX';
    const INDEX_FULLTEXT = 'FULLTEXT';
    const INDEX_UNIQUE = 'UNIQUE';
    
    private $type = self::TYPE_VARCHAR;
    private $length = 255;
    private $values = [];
    private $default = null;
    private $nullable = true;
    private $isLangField = false;
    private $autoIncrement = false;
    private $primaryKey = false;
    private $indexes = [];
    private $fieldName;

    public function __construct($name)
    {
        $this->fieldName = preg_replace('~[\W]~', '', $name);
    }

    public function isLangField()
    {
        return $this->isLangField;
    }

    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    public function setAutoIncrement()
    {
        $this->setIndexPrimaryKey();
        $this->autoIncrement = true;
        return $this;
    }

    public function unsetAutoIncrement()
    {
        $this->autoIncrement = false;
        return $this;
    }

    public function getType()
    {
        $type = $this->type;
        
        if ($this->type === self::TYPE_ENUM && !empty($this->values)) {
            $values = array_map(function ($value) {
                return "'" . $value . "'";
            }, $this->values);
            $type .= '(' . implode(',', $values) . ')';
        } elseif (!empty($this->length)) {
            $type .= "({$this->length})";
        }
        
        return $type;
    }

    public function isNullable()
    {
        return $this->nullable === true;
    }

    public function isNotNullable()
    {
        return $this->nullable === false;
    }

    public function setNullable()
    {
        $this->nullable = true;
        return $this;
    }

    public function unsetNullable()
    {
        $this->nullable = false;
        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setIsLang()
    {
        $this->isLangField = true;
        return $this;
    }

    public function unsetIsLang()
    {
        $this->isLangField = false;
        return $this;
    }

    public function setTypeEnum(array $values, $nullable = true)
    {
        $this->resetAll();
        $this->values = array_unique($values);
        $this->type = self::TYPE_ENUM;
        $this->nullable = $nullable;
        return $this;
    }

    public function setTypeTimestamp($nullable = true, $default = 'current_timestamp()')
    {
        $this->resetAll();
        $this->default = $default;
        $this->type = self::TYPE_TIMESTAMP;
        $this->nullable = $nullable;
        return $this;
    }

    public function setTypeDatetime($nullable = true)
    {
        $this->resetAll();
        $this->type = self::TYPE_DATETIME;
        $this->nullable = $nullable;
        return $this;
    }
    
    public function setTypeVarchar($length, $nullable = true)
    {
        if (!is_int($length)) {
            throw new Exception("Length must be integer");
        }

        $this->resetAll();
        
        $this->type = self::TYPE_VARCHAR;
        $this->nullable = $nullable;
        $this->length = $length;
        return $this;
    }

    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }
    
    public function setTypeInt($length, $nullable = true)
    {
        if (!is_int($length)) {
            throw new Exception("Length must be integer");
        }

        $this->resetAll();
        
        $this->type = self::TYPE_INT;
        $this->nullable = $nullable;
        $this->length = $length;
        return $this;
    }
    
    public function setTypeTinyInt($length, $nullable = true)
    {
        if (!is_int($length)) {
            throw new Exception("Length must be integer");
        }

        $this->resetAll();
        
        $this->type = self::TYPE_TINYINT;
        $this->nullable = $nullable;
        $this->length = $length;
        return $this;
    }
    
    public function setTypeFloat($length, $nullable = true)
    {
        $this->resetAll();
        
        $this->type = self::TYPE_FLOAT;
        $this->nullable = $nullable;
        $this->length = $length;
        return $this;
    }
    
    public function setTypeDecimal($length, $nullable = true)
    {
        $this->resetAll();
        
        $this->type = self::TYPE_DECIMAL;
        $this->nullable = $nullable;
        $this->length = $length;
        return $this;
    }
    
    public function setTypeText()
    {
        $this->resetAll();
        $this->type = self::TYPE_TEXT;
        return $this;
    }
    
    public function setTypeMediumText()
    {
        $this->resetAll();
        $this->type = self::TYPE_MEDIUMTEXT;
        return $this;
    }
    
    public function setTypeLongText()
    {
        $this->resetAll();
        $this->type = self::TYPE_LONGTEXT;
        return $this;
    }
    
    public function getName()
    {
        return $this->fieldName;
    }

    public function isPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function setIndexPrimaryKey()
    {
        $this->primaryKey = true;
        return $this;
    }

    /**
     * @param null $length
     * @param EntityField ...$fields Экземпляры класса EntityField в сочетании с которыми нужно сделать
     * составной индекс
     * @return $this
     */
    public function setIndex($length = null, EntityField ...$fields)
    {
        $this->indexes[self::INDEX] = [
            'length' => $length,
            'fields' => $fields,
        ];
        return $this;
    }

    public function setIndexFulltext()
    {
        $this->indexes[self::INDEX_FULLTEXT] = [
            'length' => null,
        ];
        return $this;
    }

    /**
     * @param null $length
     * @param EntityField ...$fields Экземпляры класса EntityField в сочетании с которыми нужно сделать 
     * составной индекс
     * @return $this
     */
    public function setIndexUnique($length = null, EntityField ...$fields)
    {
        $this->indexes[self::INDEX_UNIQUE] = [
            'length' => $length,
            'fields' => $fields,
        ];
        return $this;
    }
    
    public function getIndexes()
    {
        return $this->indexes;
    }
    
    public function unsetPrimaryKey()
    {
        $this->primaryKey = false;
        return $this;
    }
    
    private function resetAll()
    {
        $this->type = null;
        $this->length = null;
        $this->values = null;
        $this->default = null;
        $this->nullable = false;
    }
    
}