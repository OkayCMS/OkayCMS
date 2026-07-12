<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Security\BackendFileDownloadPolicy;
use PHPUnit\Framework\TestCase;

final class BackendProtectedFileDownloadTest extends TestCase
{
    public function testKnownExportsMapToSpecificPermissions(): void
    {
        $policy = new BackendFileDownloadPolicy();

        self::assertSame('export', $policy->permissionFor('export', 'export', 'csv'));
        self::assertSame('orders', $policy->permissionFor('export', 'export_orders', 'csv'));
        self::assertSame('users', $policy->permissionFor('export_users', 'users', 'csv'));
        self::assertSame('subscribes', $policy->permissionFor('export_users', 'subscribes', 'csv'));
        self::assertSame('import', $policy->permissionFor('import', 'example', 'csv'));
    }

    public function testUnknownFoldersFilesAndExtensionsAreDenied(): void
    {
        $policy = new BackendFileDownloadPolicy();

        self::assertNull($policy->permissionFor('export', 'unknown', 'csv'));
        self::assertNull($policy->permissionFor('unknown', 'export', 'csv'));
        self::assertNull($policy->permissionFor('export', 'export', 'php'));
    }
}
