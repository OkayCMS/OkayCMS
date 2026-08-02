<?php

declare(strict_types=1);

namespace Okay\Core\Packaging;

use Okay\Core\Filesystem\FilesystemPath;
use RuntimeException;

final class ProdIgnore
{
    /** @var list<string> */
    private array $prefixPatterns = [];

    /** @var list<string> */
    private array $exactPatterns = [];

    private function __construct()
    {
    }

    public static function fromProjectRoot(string $projectRoot): self
    {
        return self::fromFile(FilesystemPath::join($projectRoot, '.prodignore'));
    }

    public static function fromFile(string $filePath): self
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("Unable to read .prodignore: {$filePath}");
        }

        $instance = new self();
        $instance->loadPatterns($content);

        return $instance;
    }

    public function matches(string $relativePath): bool
    {
        $relativePath = FilesystemPath::normalizeRelative($relativePath);
        if ($relativePath === '') {
            return false;
        }

        if (in_array($relativePath, $this->exactPatterns, true)) {
            return true;
        }

        foreach ($this->prefixPatterns as $prefix) {
            if ($relativePath === $prefix || str_starts_with($relativePath, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }

    private function loadPatterns(string $content): void
    {
        foreach (preg_split('/\R/', $content) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $line = FilesystemPath::normalizeRelative($line);
            if (str_ends_with($line, '/')) {
                $this->prefixPatterns[] = rtrim($line, '/');
                continue;
            }

            $this->exactPatterns[] = $line;
        }
    }
}
