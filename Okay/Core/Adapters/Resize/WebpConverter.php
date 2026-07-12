<?php

declare(strict_types=1);

namespace Okay\Core\Adapters\Resize;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final class WebpConverter
{
    private ImageManager $manager;

    public function __construct(private readonly int $imageQuality = 80)
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function convert(string $source, string $destination): bool
    {
        $temporaryDestination = $destination . '.tmp';

        try {
            $image = $this->manager->decodePath($source);
            $image->encodeUsingFileExtension('webp', quality: $this->imageQuality)->save($temporaryDestination);

            if (!is_file($temporaryDestination)) {
                return false;
            }

            if (is_file($destination) && !unlink($destination)) {
                unlink($temporaryDestination);
                return false;
            }

            return rename($temporaryDestination, $destination);
        } catch (Throwable) {
            if (is_file($temporaryDestination)) {
                unlink($temporaryDestination);
            }

            return false;
        }
    }
}
