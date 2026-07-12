<?php

declare(strict_types=1);

namespace Okay\Core\Security;

final class BackendFileDownloadPolicy
{
    private const FILE_PERMISSIONS = [
        'export' => [
            'export' => 'export',
            'export_orders' => 'orders',
            'export_stat' => 'category_stats',
            'export_stat_products' => 'sales_report',
        ],
        'export_users' => [
            'users' => 'users',
            'subscribes' => 'subscribes',
        ],
        'import' => [
            'example' => 'import',
        ],
        'watermark' => [
            '*' => 'settings',
        ],
    ];

    private const EXTENSIONS = [
        'csv',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'tif',
        'bmp',
        'ico',
    ];

    public function permissionFor(string $folder, string $file, string $extension): ?string
    {
        if (!in_array($extension, self::EXTENSIONS, true)) {
            return null;
        }

        return self::FILE_PERMISSIONS[$folder][$file] ?? self::FILE_PERMISSIONS[$folder]['*'] ?? null;
    }
}
