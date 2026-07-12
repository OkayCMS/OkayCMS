<?php

namespace Entities;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\CategoriesEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CategoryFilterIdNormalizationTest extends TestCase
{
    private EntityFactory $entityFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityFactory = ServiceLocator::getInstance()->getService(EntityFactory::class);
    }

    /**
     * @param array<string, mixed> $filter
     */
    #[DataProvider('dirtyCategoryIdFiltersProvider')]
    public function testCategoriesEntityIgnoresDirtyIdFiltersWithoutDeprecation(array $filter): void
    {
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);

        self::assertSame([], $categoriesEntity->find($filter));
    }

    /**
     * @param array<string, mixed> $filter
     */
    #[DataProvider('dirtyCategoryIdFiltersProvider')]
    public function testBlogCategoriesEntityIgnoresDirtyIdFiltersWithoutDeprecation(array $filter): void
    {
        $categoriesEntity = $this->entityFactory->get(BlogCategoriesEntity::class);

        self::assertSame([], $categoriesEntity->find($filter));
    }

    /**
     * @return array<string, array{0: array<string, mixed>}>
     */
    public static function dirtyCategoryIdFiltersProvider(): array
    {
        return [
            'array-form null id' => [
                ['id' => [null]],
            ],
            'mixed empty id values' => [
                ['id' => [null, '', false, 0, '0']],
            ],
            'scalar null id' => [
                ['id' => null],
            ],
        ];
    }
}
