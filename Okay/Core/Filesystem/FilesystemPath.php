<?php

declare(strict_types=1);

namespace Okay\Core\Filesystem;

final class FilesystemPath
{
    public static function directoryWithTrailingSlash(string $path): string
    {
        return rtrim(str_replace('\\', '/', $path), '/') . '/';
    }

    public static function join(string $base, string $segment): string
    {
        return self::directoryWithTrailingSlash($base) . ltrim(str_replace('\\', '/', $segment), '/');
    }

    public static function normalizeRelative(string $relativePath): string
    {
        return ltrim(str_replace('\\', '/', $relativePath), '/');
    }
}
