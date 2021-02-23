<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class SupportInfoEntity extends Entity
{
    private $info;

    protected static $fields = [
        'id',
        'temp_key',
        'temp_time',
        'new_messages',
        'balance',
        'private_key',
        'public_key',
        'is_auto',
        'accesses',
    ];

    protected static $table = '__support_info';
    protected static $tableAlias = 'si';

    public function getInfo()
    {
        if (!empty($this->info)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], $this->info, func_get_args());
        }

        $this->setUp();
        $this->select->cols($this->getAllFields())->limit(1);
        $this->db->query($this->select);        
        $info = $this->getResult();

        if (!empty($info)) {
            $this->info = $info;
            return ExtenderFacade::execute([static::class, __FUNCTION__], $this->info, func_get_args());
        }

        $this->clearInfo();
        $this->info = [
            'private_key'  => null,
            'public_key'   => null,
            'new_messages' => 0,
            'balance'      => 0,
            'temp_key'     => null,
            'temp_time'    => null,
            'is_auto'      => 1
        ];
        $this->addInfo($this->info);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->info, func_get_args());
    }

    private function clearInfo()
    {
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("TRUNCATE ".self::getTable());
        $result = (bool) $this->db->query($sql);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    private function addInfo($info)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into(self::getTable());
        $insert->cols((array) $info);
        $result = (bool) $this->db->query($insert);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    public function updateInfo($info)
    {
        unset($this->info);

        $update = $this->queryFactory->newUpdate();
        $update->table(self::getTable());
        $update->cols((array) $info);
        $result = (bool) $this->db->query($update);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    public function get($id)
    {
        throw new \Exception("Method get() in SupportInfoEntity is not allowed");
    }

    public function add($info)
    {
        throw new \Exception("Method add() in SupportInfoEntity is not allowed");
    }

    public function update($id, $info)
    {
        throw new \Exception("Method update() in SupportInfoEntity is not allowed");
    }

    public function delete($id)
    {
        throw new \Exception("Method delete() in SupportInfoEntity is not allowed");
    }
}
