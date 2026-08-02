<?php

declare(strict_types=1);

namespace Okay\Core\Runtime;

use Okay\Core\Design;
use Okay\Core\Filesystem\DirectoryClearResult;
use Okay\Core\Filesystem\FilesystemPath;
use Okay\Core\Filesystem\KeepFolderDirectoryCleaner;

final class RuntimeArtifactsClearer
{
    public function __construct(
        private readonly Design $design,
        private readonly KeepFolderDirectoryCleaner $keepFolderDirectoryCleaner,
        private readonly string $installRoot,
        private readonly string $compileCssDir,
        private readonly string $compileJsDir
    ) {
    }

    public function clear(): DirectoryClearResult
    {
        $result = new DirectoryClearResult();

        $this->runInInstallRoot(function (): void {
            $this->design->clearCache();
        });

        foreach ($this->generatedDirectoryPaths() as $directory) {
            $result->merge($this->keepFolderDirectoryCleaner->clearDirectory($directory));
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    private function generatedDirectoryPaths(): array
    {
        return [
            $this->installPath('compiled'),
            $this->installPath('backend/design/compiled'),
            $this->installPath('Okay/xml/compiled'),
            $this->installPath($this->compileCssDir),
            $this->installPath($this->compileJsDir),
        ];
    }

    private function installPath(string $relativePath): string
    {
        return FilesystemPath::join($this->installRoot, $relativePath);
    }

    private function runInInstallRoot(callable $callback): void
    {
        $previousWorkingDirectory = getcwd();
        if ($previousWorkingDirectory === false) {
            $previousWorkingDirectory = $this->installRoot;
        }

        chdir($this->installRoot);

        try {
            $callback();
        } finally {
            chdir($previousWorkingDirectory);
        }
    }
}
