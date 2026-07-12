<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

final class DatabaseUpdateFile
{
    public function __construct(
        private readonly string $version,
        private readonly string $path
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
