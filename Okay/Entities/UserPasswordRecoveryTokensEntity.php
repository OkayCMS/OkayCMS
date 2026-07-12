<?php

declare(strict_types=1);

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class UserPasswordRecoveryTokensEntity extends Entity
{
    protected static $fields = [
        'id',
        'user_id',
        'token_digest',
        'expires_at',
        'consumed_at',
        'created',
        'created_ip',
    ];

    protected static $defaultOrderFields = [
        'id DESC',
    ];

    protected static $table = '__user_password_recovery_tokens';
    protected static $tableAlias = 'uprt';
    protected static $alternativeIdField = 'token_digest';
    protected static $langTable;
    protected static $langObject;

    public function createToken(int $userId, string $tokenDigest, string $expiresAt, string $createdIp = ''): int|false
    {
        $this->deleteActiveForUser($userId);

        $id = $this->add([
            'user_id' => $userId,
            'token_digest' => $tokenDigest,
            'expires_at' => $expiresAt,
            'created' => 'NOW()',
            'created_ip' => $createdIp,
        ]);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $id, func_get_args());
    }

    public function getActiveByDigest(string $tokenDigest, ?int $now = null): object|false
    {
        $now ??= time();
        $select = $this->queryFactory->newSelect();
        $select->from($this->getTable() . ' AS ' . $this->getTableAlias())
            ->cols($this->getAllFields())
            ->where($this->getTableAlias() . '.token_digest = :token_digest')
            ->where($this->getTableAlias() . '.consumed_at IS NULL')
            ->where($this->getTableAlias() . '.expires_at >= :now')
            ->limit(1)
            ->bindValue('token_digest', $tokenDigest)
            ->bindValue('now', date('Y-m-d H:i:s', $now));

        $this->db->query($select);
        $token = $this->getResult();

        return ExtenderFacade::execute([static::class, __FUNCTION__], $token ?: false, func_get_args());
    }

    public function getActiveById(int $id, int $userId, ?int $now = null): object|false
    {
        $now ??= time();
        $select = $this->queryFactory->newSelect();
        $select->from($this->getTable() . ' AS ' . $this->getTableAlias())
            ->cols($this->getAllFields())
            ->where($this->getTableAlias() . '.id = :id')
            ->where($this->getTableAlias() . '.user_id = :user_id')
            ->where($this->getTableAlias() . '.consumed_at IS NULL')
            ->where($this->getTableAlias() . '.expires_at >= :now')
            ->limit(1)
            ->bindValue('id', $id)
            ->bindValue('user_id', $userId)
            ->bindValue('now', date('Y-m-d H:i:s', $now));

        $this->db->query($select);
        $token = $this->getResult();

        return ExtenderFacade::execute([static::class, __FUNCTION__], $token ?: false, func_get_args());
    }

    public function consume(int $id, int $userId, ?int $now = null): bool
    {
        $now ??= time();
        $update = $this->queryFactory->newUpdate();
        $update->table($this->getTable())
            ->set('consumed_at', 'NOW()')
            ->where('id = :id')
            ->where('user_id = :user_id')
            ->where('consumed_at IS NULL')
            ->where('expires_at >= :now')
            ->bindValue('id', $id)
            ->bindValue('user_id', $userId)
            ->bindValue('now', date('Y-m-d H:i:s', $now));

        $this->db->query($update);
        $consumed = $this->db->affectedRows() === 1;

        return ExtenderFacade::execute([static::class, __FUNCTION__], $consumed, func_get_args());
    }

    public function deleteActiveForUser(int $userId): bool
    {
        $update = $this->queryFactory->newUpdate();
        $update->table($this->getTable())
            ->set('consumed_at', 'NOW()')
            ->where('user_id = :user_id')
            ->where('consumed_at IS NULL')
            ->bindValue('user_id', $userId);

        $this->db->query($update);

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }
}
