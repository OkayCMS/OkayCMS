<?php

declare(strict_types=1);

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class UserAuthAttemptsEntity extends Entity
{
    public const ACTION_LOGIN = 'login';
    public const ACTION_PASSWORD_REMIND = 'password_remind';
    public const ACTION_SUPPORT_ENDPOINT = 'support_endpoint';

    private const LOGIN_LIMIT = 10;
    private const RECOVERY_LIMIT = 5;
    private const SUPPORT_ENDPOINT_LIMIT = 20;
    private const WINDOW_SECONDS = 900;

    protected static $fields = [
        'id',
        'action',
        'email_hash',
        'ip_hash',
        'attempts',
        'first_attempt',
        'last_attempt',
    ];

    protected static $defaultOrderFields = [
        'id DESC',
    ];

    protected static $table = '__user_auth_attempts';
    protected static $tableAlias = 'uaa';
    protected static $langTable;
    protected static $langObject;

    public function isBlocked(string $action, string $email, string $ip, ?int $now = null): bool
    {
        $attempt = $this->getCurrentAttempt($action, $email, $ip, $now);
        if ($attempt === false) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        /** @var object{attempts: int|string} $attempt */
        $blocked = (int)$attempt->attempts >= $this->limitFor($action);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $blocked, func_get_args());
    }

    public function registerFailure(string $action, string $email, string $ip, ?int $now = null): void
    {
        $now ??= time();
        $attempt = $this->getCurrentAttempt($action, $email, $ip, $now);
        if ($attempt === false) {
            $this->deleteByKey($action, $email, $ip);
            $this->add([
                'action' => $action,
                'email_hash' => $this->emailHash($email),
                'ip_hash' => $this->ipHash($ip),
                'attempts' => 1,
                'first_attempt' => date('Y-m-d H:i:s', $now),
                'last_attempt' => date('Y-m-d H:i:s', $now),
            ]);

            return;
        }

        /** @var object{id: int|string, attempts: int|string} $attempt */
        $this->update((int)$attempt->id, [
            'attempts' => (int)$attempt->attempts + 1,
            'last_attempt' => date('Y-m-d H:i:s', $now),
        ]);
    }

    public function clear(string $action, string $email, string $ip): void
    {
        $this->deleteByKey($action, $email, $ip);
    }

    private function getCurrentAttempt(string $action, string $email, string $ip, ?int $now = null): object|false
    {
        $now ??= time();
        $select = $this->queryFactory->newSelect();
        $select->from($this->getTable() . ' AS ' . $this->getTableAlias())
            ->cols($this->getAllFields())
            ->where($this->getTableAlias() . '.action = :action')
            ->where($this->getTableAlias() . '.email_hash = :email_hash')
            ->where($this->getTableAlias() . '.ip_hash = :ip_hash')
            ->where($this->getTableAlias() . '.first_attempt >= :window_start')
            ->limit(1)
            ->bindValue('action', $action)
            ->bindValue('email_hash', $this->emailHash($email))
            ->bindValue('ip_hash', $this->ipHash($ip))
            ->bindValue('window_start', date('Y-m-d H:i:s', $now - self::WINDOW_SECONDS));

        $this->db->query($select);
        $attempt = $this->getResult();

        return $attempt ?: false;
    }

    private function limitFor(string $action): int
    {
        if ($action === self::ACTION_PASSWORD_REMIND) {
            return self::RECOVERY_LIMIT;
        }

        if ($action === self::ACTION_SUPPORT_ENDPOINT) {
            return self::SUPPORT_ENDPOINT_LIMIT;
        }

        return self::LOGIN_LIMIT;
    }

    private function emailHash(string $email): string
    {
        return hash('sha256', mb_strtolower(trim($email)));
    }

    private function ipHash(string $ip): string
    {
        return hash('sha256', trim($ip));
    }

    private function deleteByKey(string $action, string $email, string $ip): void
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from($this->getTable())
            ->where('action = :action')
            ->where('email_hash = :email_hash')
            ->where('ip_hash = :ip_hash')
            ->bindValue('action', $action)
            ->bindValue('email_hash', $this->emailHash($email))
            ->bindValue('ip_hash', $this->ipHash($ip));

        $this->db->query($delete);
    }
}
