<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class FeedFilterOperatorSecurityTest extends TestCase
{
    public function testRuntimeFeedAdaptersNormalizeSqlComparisonOperators(): void
    {
        $root = dirname(__DIR__, 2);
        $adapterFiles = [
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/FacebookAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/GoogleMerchantAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/HotlineAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/PriceUaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/PromUaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/RozetkaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/Adapters/YmlAdapter.php',
        ];

        foreach ($adapterFiles as $adapterFile) {
            $source = file_get_contents($root . '/' . $adapterFile);

            self::assertIsString($source, $adapterFile);
            self::assertStringContainsString('normalizeComparisonOperator', $source, $adapterFile);
            self::assertStringNotContainsString(
                "\$operator = \$this->feed->settings['filter_price']['operator'];",
                $source,
                $adapterFile
            );
            self::assertStringNotContainsString(
                "\$operator = \$this->feed->settings['filter_stock']['operator'];",
                $source,
                $adapterFile
            );
        }
    }

    public function testBackendFeedAdaptersNormalizePostedComparisonOperators(): void
    {
        $root = dirname(__DIR__, 2);
        $adapterFiles = [
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendFacebookAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendGoogleMerchantAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendHotlineAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendPriceUaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendPromUaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendRozetkaAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/Adapters/BackendYmlAdapter.php',
        ];

        foreach ($adapterFiles as $adapterFile) {
            $source = file_get_contents($root . '/' . $adapterFile);

            self::assertIsString($source, $adapterFile);
            self::assertStringContainsString('normalizeComparisonOperator', $source, $adapterFile);
            self::assertStringNotContainsString(
                "'operator' => \$postSettings['filter_price']['operator']",
                $source,
                $adapterFile
            );
            self::assertStringNotContainsString(
                "'operator' => \$postSettings['filter_stock']['operator']",
                $source,
                $adapterFile
            );
        }
    }

    public function testComparisonOperatorAllowlistIsNarrow(): void
    {
        $root = dirname(__DIR__, 2);

        foreach ([
            'Okay/Modules/OkayCMS/Feeds/Core/Presets/AbstractPresetAdapter.php',
            'Okay/Modules/OkayCMS/Feeds/Backend/Core/Presets/AbstractBackendPresetAdapter.php',
        ] as $baseAdapterFile) {
            $source = file_get_contents($root . '/' . $baseAdapterFile);

            self::assertIsString($source, $baseAdapterFile);
            self::assertStringContainsString("in_array(\$operator, ['<', '>', '='], true)", $source, $baseAdapterFile);
        }
    }
}
