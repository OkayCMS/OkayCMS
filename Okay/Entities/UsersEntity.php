<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class UsersEntity extends Entity
{

    protected static $fields = [
        'id',
        'email',
        'password',
        'name',
        'last_name',
        'phone',
        'address',
        'group_id',
        'last_ip',
        'created',
        'remind_code',
        'remind_expire',
        'preferred_delivery_id',
        'preferred_payment_method_id',
        'g.discount',
        'g.name as group_name',
    ];

    protected static $searchFields = [
        'name',
        'email',
        'last_ip',
    ];

    protected static $defaultOrderFields = [
        'name',
    ];

    protected static $table = '__users';
    protected static $tableAlias = 'u';
    protected static $alternativeIdField = 'email';
    protected static $langTable;
    protected static $langObject;

    // осторожно, при изменении соли испортятся текущие пароли пользователей
    private $salt = '8e86a279d6e182b3c811c559e6b15484';
    
    public function find(array $filter = [])
    {
        $this->select->join('LEFT', '__groups AS g', 'u.group_id=g.id');
        return parent::find($filter);
    }
    
    public function get($id)
    {
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $this->select->join('LEFT', '__groups AS g', 'u.group_id=g.id');
        
        $user = parent::get($id);
        
        if (empty($user)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $user->discount = floor($user->discount);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $user, func_get_args());
    }

    public function add($user)
    {
        $user = (array)$user;
        if (isset($user['password'])) {
            $user['password'] = md5($this->salt . $user['password'] . md5($user['password']));
        }
        
        $count = $this->count(['email'=>$user['email']]);
        
        if ($count > 0) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }
        
        return parent::add($user);
    }

    public function update($id, $user)
    {
        $user = (array)$user;
        if (isset($user['password'])) {
            $user['password'] = md5($this->salt . $user['password'] . md5($user['password']));
        }
        
        return parent::update($id, $user);
    }

    public function delete($ids)
    {
        if (!empty($ids)) {
            $update = $this->queryFactory->newUpdate();
            $update->table('__orders')
                ->set('user_id', 0)
                ->where('user_id IN (:user_id)')
                ->bindValue('user_id', $ids);
            
            $this->db->query($update);
            
        }

        return parent::delete($ids);
    }

    /**
     * @param string $email
     * @param string $password
     * @return int|false
     */
    public function checkPassword($email, $password)
    {
        $encPassword = md5($this->salt . $password . md5($password));
        $userId = $this->cols(['id'])->findOne([
            'email' => $email,
            'password' => $encPassword,
            'limit' => 1,
        ]);
        if (!empty($userId)) {
            $userId = (int)$userId;
            return ExtenderFacade::execute([static::class, __FUNCTION__], $userId, func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }

    public function generatePass($passLen = 6) {
        $pass = '';
        for ($i=0; $i< $passLen; $i++) {
            $ranges = [
                rand(48, 57),
                rand(65, 90),
                rand(97, 122),
            ];
            $pass .= chr($ranges[rand(0, 2)]);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $pass, func_get_args());
    }

    public function getULoginUser($token)
    {
        $s = file_get_contents('https://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
        $result = json_decode($s, true);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {
        switch ($order) {
            case 'date':
                $orderFields = ['u.created DESC'];
                break;
            case 'cnt_order':
                $orderFields = ["(select count(o.id) from __orders o where o.user_id = u.id) DESC"];
                break;
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }
}
