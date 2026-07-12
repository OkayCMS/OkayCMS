<?php

declare(strict_types=1);

namespace Okay\Core\Security;

final class CustomerSession
{
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }

        CustomerCsrfToken::rotate();
    }
}
