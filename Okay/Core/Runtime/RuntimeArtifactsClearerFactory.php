<?php

declare(strict_types=1);

namespace Okay\Core\Runtime;

use Okay\Core\Filesystem\DirectoryClearResult;
use Okay\Core\Filesystem\FilesystemPath;
use Okay\Core\Filesystem\KeepFolderDirectoryCleaner;
use Okay\Core\Design;
use RuntimeException;

final class RuntimeArtifactsClearerFactory
{
    public function __construct(
        private readonly Design $design,
        private readonly KeepFolderDirectoryCleaner $keepFolderDirectoryCleaner,
        private readonly string $compileCssDir,
        private readonly string $compileJsDir
    ) {
    }

    public static function clearInstallRoot(string $codeRoot, string $installRoot): DirectoryClearResult
    {
        $codeRoot = FilesystemPath::directoryWithTrailingSlash($codeRoot);
        $installRoot = FilesystemPath::directoryWithTrailingSlash($installRoot);

        if (!is_file($codeRoot . 'vendor/autoload.php')) {
            throw new RuntimeException('Composer dependencies are required to clear runtime artifacts.');
        }

        /** @var \Okay\Core\OkayContainer\OkayContainer $container */
        $container = include $codeRoot . 'Okay/Core/config/container.php';

        /** @var self $factory */
        $factory = $container->get(self::class);

        return $factory->forInstallRoot($installRoot)->clear();
    }

    public function forInstallRoot(string $installRoot): RuntimeArtifactsClearer
    {
        return new RuntimeArtifactsClearer(
            $this->design,
            $this->keepFolderDirectoryCleaner,
            FilesystemPath::directoryWithTrailingSlash($installRoot),
            $this->compileCssDir,
            $this->compileJsDir
        );
    }
}
