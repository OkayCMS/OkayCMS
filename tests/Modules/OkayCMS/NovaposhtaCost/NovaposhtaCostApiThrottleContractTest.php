<?php

declare(strict_types=1);

namespace Modules\OkayCMS\NovaposhtaCost;

use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class NovaposhtaCostApiThrottleContractTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testApiRequestThrottlingIsOptInForBulkCacheUpdates(): void
    {
        $method = new ReflectionMethod(NPApiHelper::class, 'request');
        $parameters = $method->getParameters();

        self::assertCount(3, $parameters);
        self::assertSame('throttleRequests', $parameters[2]->getName());
        self::assertTrue($parameters[2]->isDefaultValueAvailable());
        self::assertFalse($parameters[2]->getDefaultValue());

        $apiHelper = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/Helpers/NPApiHelper.php');

        self::assertStringContainsString('private const THROTTLE_DELAY_MICROSECONDS = 500000;', $apiHelper);
        self::assertStringContainsString('if ($throttleRequests) {', $apiHelper);
        self::assertStringContainsString('usleep(self::THROTTLE_DELAY_MICROSECONDS);', $apiHelper);
        self::assertStringNotContainsString("\$response = curl_exec(\$ch);\n        usleep(", $apiHelper);
    }

    public function testCacheUpdatesUseThrottledApiRequests(): void
    {
        $cacheHelper = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/Helpers/NPCacheHelper.php');

        self::assertStringContainsString('$this->apiHelper->getCities($page, $limit, true)', $cacheHelper);
        self::assertStringContainsString(
            '$this->apiHelper->getWarehouses($warehouseType, $page, $limit, true)',
            $cacheHelper
        );
        self::assertStringContainsString('$this->apiHelper->getWarehouseTypes(true)', $cacheHelper);
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);

        return $source;
    }
}
