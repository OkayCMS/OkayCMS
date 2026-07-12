<?php

namespace Core;

use Okay\Core\Entity\Entity;
use Okay\Core\Languages;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class LanguagesEntityRegistryTest extends TestCase
{
    public function testCoreEntityRegistryCoversAllCoreMultilingualEntities(): void
    {
        $languages = $this->languagesWithoutDatabase();
        $expectedTables = [];

        foreach (glob('Okay/Entities/*Entity.php') ?: [] as $file) {
            $class = 'Okay\\Entities\\' . basename($file, '.php');
            if (!is_subclass_of($class, Entity::class)) {
                continue;
            }

            /** @var class-string<Entity> $class */
            if (!$class::getLangTable()) {
                continue;
            }

            $expectedTables[] = $class::getTable();
        }

        sort($expectedTables);

        $registeredTables = [];
        foreach ($languages->getEntitiesLangInfo() as $info) {
            if (!in_array($info->table, $expectedTables, true)) {
                continue;
            }

            $registeredTables[] = $info->table;
        }

        sort($registeredTables);

        self::assertSame($expectedTables, $registeredTables);
    }

    public function testEntityLangInfoKeepsExistingContractShape(): void
    {
        $languages = $this->languagesWithoutDatabase();
        $infoByTable = [];

        foreach ($languages->getEntitiesLangInfo() as $info) {
            $infoByTable[$info->table] = $info;
        }

        self::assertArrayHasKey('__products', $infoByTable);
        self::assertSame('__lang_products', $infoByTable['__products']->langTable);
        self::assertSame('product', $infoByTable['__products']->object);
        $expectedCoreProductFields = [
            'name',
            'annotation',
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'special',
        ];

        self::assertSame(
            $expectedCoreProductFields,
            array_slice($infoByTable['__products']->fields, 0, count($expectedCoreProductFields))
        );
    }

    private function languagesWithoutDatabase(): Languages
    {
        $reflection = new ReflectionClass(Languages::class);

        $languages = $reflection->newInstanceWithoutConstructor();
        self::assertInstanceOf(Languages::class, $languages);

        return $languages;
    }
}
