<?php


namespace Okay\Core;


use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryInterface;
use Psr\Log\LoggerInterface;
use PDOStatement;

class Database
{

    /**
     * @var \PDOStatement
     */
    private $result;

    /**
     * @var ExtendedPdo
     */
    private $pdo;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;
    private $rev;
    private $dbParams;
    private $affectedRows = null;
    
    /**
     * Database constructor.
     * @param $pdo ExtendedPdo
     * @param $logger LoggerInterface
     * @param $dbParams array
     * @param $queryFactory QueryFactory
     * @throws \Exception
     */
    public function __construct(ExtendedPdo $pdo, LoggerInterface $logger, $dbParams, QueryFactory $queryFactory)
    {
        
        $this->pdo          = $pdo;
        $this->logger       = $logger;
        $this->dbParams     = (object)$dbParams;
        $this->queryFactory = $queryFactory;

        $this->pdo->connect();

        if (!empty($this->dbParams->db_names)) {
            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("SET NAMES '{$this->dbParams->db_names}'");
            $this->query($sql);
        }

        if (!empty($this->dbParams->db_sql_mode)) {
            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement('SET SESSION SQL_MODE = "' . $this->dbParams->db_sql_mode . '"');
            $this->query($sql);
        }
        
        if (!empty($this->dbParams->db_timezone)) {
            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement('SET time_zone = "' . $this->dbParams->db_timezone . '"');
            $this->query($sql);
        }
    }
    
    /**
     * В деструкторе отсоединяемся от базы
     */
    public function __destruct()
    {
        $this->pdo->disconnect();
    }
    
    /**
     * @param QueryInterface $query
     * @param bool $debug
     * @return bool
     */
    public function query(QueryInterface $query, $debug = false)
    {
        $result = true;
        try {
            $this->affectedRows = null;
            
            // Получаем все плейсхолдеры
            $bind = $query->getBindValues();

            // Подготавливаем запрос для выполнения добавляя данные из плейсхолдеров
            $this->result = $this->pdo->perform(
                $this->tablePrefix($query),
                $bind
            );

            $this->affectedRows = $this->result->rowCount();

            if ($debug === true) {
                if ($queryString = $this->debug($this->result, $bind)) {
                    print "<pre>" . $queryString . "</pre>" . PHP_EOL . PHP_EOL;
                } else {
                    print 'Error in query' . PHP_EOL . PHP_EOL;
                }
            }
        } catch (\Exception $e) {
            $log = 'Sql query error: "' . $e->getMessage() . '"' . PHP_EOL;
            $log .= 'Query trace:' . PHP_EOL;
            $trace = $e->getTrace();
            foreach ($trace as $value) {
                if (isset($value['class'])) {
                    $log .= $value['class'] . "->";
                }
                if (isset($value['function'])) {
                    $log .= $value['function'] . "();";
                }
                if (isset($value['line'])) {
                    $log .= "-line " . $value['line'];
                }
                $log .= PHP_EOL;
            }
            $this->logger->error($log);
            $result = false;
        }
        return $result;
    }

    public function prepare(QueryInterface $query, $values)
    {
        return $this->pdo->prepareWithValues(
            $this->tablePrefix($query), 
            $values
        );
    }
    
    /**
     * @param PDOStatement $preparedQuery
     * @param array $bindValues
     * @return string
     * ВНИМАНИЕ: данный метод не возвращает запрос, который выполнял MySQL сервер
     * он лиш имитирует такой же запрос, не исключено что в определенных ситуациях это будут разные запросы
     */
    public function debug(PDOStatement $preparedQuery, $bindValues = [])
    {
        $binded = [];
        if (!empty($bindValues)) {
            foreach ($bindValues as $k => &$b) {
                // Если фильтруют по IN (:id) и в качестве id передали массив,
                // такой плейсхолдер при вызове perform() заменился на IN (:id_0, :id_1, :id_2, :id_3, :id_4)
                // здесь добавляем значения всем суб плейсхолдерам
                if (is_array($b)) {
                    $placeholderNum = 0;
                    foreach ($b as $kv => $v) {
                        //$preparedQuery->bindValue($k . '_' . ($placeholderNum), $v);
                        $binded[$k . '_' . ($placeholderNum)] = $v;
                        $placeholderNum++;
                    }
                } else {
                    $binded[$k] = $b;
                }
            }

            foreach ($binded as $k => $b) {
                unset($binded[$k]);
                $binded[':' . $k] = $this->escape($b);
            }
        }
        return strtr($preparedQuery->queryString, $binded);
    }
    
    public function customQuery($query)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use QueryFactory::newSqlQuery for native SQL queries', E_USER_DEPRECATED);
    }
    
    private function tablePrefix($query)
    {
        if (!is_string($query) && $query instanceof QueryInterface) {
            $query = $query->getStatement();
        }
        
        return preg_replace('/([^"\'0-9a-z_])__([a-z_]+[^"\'])/i', "\$1".$this->dbParams->prefix."\$2", $query);
    }
    
    /**
     * @var $str
     * @return string
     * Экранирование строки
     */
    public function escape($str)
    {
        return $this->pdo->quote($str);
    }
    
    /**
     * Возвращает результаты запроса.
     * @param string $field - Если нужно получить массив значений одной колонки, нужно передать название этой колонки
     * @param string $mapped - Если нужно получить массив, с ключами значения другого поля (например id) нужно передать название колонки
     * @return array
     * @throws \Exception
     */
    public function results($field = null, $mapped = null)
    {
        
        if (empty($this->result)) {
            return [];
        }

        $results = [];
        $this->result->setFetchMode(ExtendedPdo::FETCH_OBJ);
        
        foreach ($this->result->fetchAll() as $row) {
            if (property_exists($row, $mapped)) {
                $mappedValue = $row->$mapped;
            } elseif (!empty($mapped)) {
                throw new \Exception("Field named \"{$mapped}\" uses for mapped is not exists");
            }

            if (!empty($field) && !property_exists($row, $field)) {
                throw new \Exception("Field named \"{$field}\" uses for select single column is not exists");
            } elseif (!empty($field) && property_exists($row, $field)) {
                $row = $row->$field;
            }
            
            if (!empty($mapped) && !empty($mappedValue)) {
                $results[$mappedValue] = $row;
            } else {
                $results[] = $row;
            }
        }
        
        return $results;
    }
    
    /**
     * Возвращает первый результат запроса.
     * @param string $field - Если нужно получить массив значений одной колонки, нужно передать название этой колонки
     * @return object|string|null
     * @throws \Exception
     */
    public function result($field = null)
    {
        if (empty($this->result)) {
            return null;
        }
        
        $row = $this->result->fetchObject();
        
        if (!empty($field) && isset($row->$field)) {
            return $row->$field;
        } elseif (!empty($field) && !isset($row->$field)) {
            return null;
        } else {
            return $row;
        }
    }
    
    /**
     * Возвращает последний вставленный id
     */
    public function insertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Возвращает количество затронутых строк
     */
    public function affectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * Вовзвращает информацию о MySQL
     *
     */
    public function getServerInfo()
    {
        $info = [];
        $info['server_version'] = $this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        $info['server_info'] = $this->pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
        return $info;
    }

    public function placehold()
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    }

    public function restore($filename)
    {
        $migration = fopen($filename, 'r');
        if(empty($migration)) {
            return;
        }

        $migrationQuery = '';
        while(!feof($migration)) {
            $line = fgets($migration);
            if ($this->isComment($line) || empty($line)) {
                continue;
            }

            $migrationQuery .= $line;
            if (!$this->isQueryEnd($line)) {
                continue;
            }

            try {
                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement($migrationQuery);
                $this->query($sql);
            } catch(\PDOException $e) {
                print 'Error performing query \'<b>'.$migrationQuery.'</b>\': '.$e->getMessage().'<br/><br/>';
            }

            $migrationQuery = '';
        }

        fclose($migration);
    }

    private function isComment($line)
    {
        return substr($line, 0, 2) == '--';
    }

    private function isQueryEnd($line)
    {
        return substr(trim($line), -1, 1) == ';';
    }
}
