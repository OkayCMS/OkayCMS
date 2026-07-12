<?php

declare(strict_types=1);

namespace Core\DebugBar;

use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DebugBar as LibDebugBar;
use Okay\Core\DebugBar\DataCollectors\ConfigCollector;
use Okay\Core\DebugBar\DataCollectors\TimeDataCollector;
use Okay\Core\DebugBar\DataFormatters\QueryFormatter;
use PHPUnit\Framework\TestCase;

final class DebugBarTest extends TestCase
{
    public function testCoreCollectorsRegisterWithStableUniqueNames(): void
    {
        $debugBar = new LibDebugBar();
        $collectors = [
            new PhpInfoCollector(),
            new MessagesCollector(),
            new RequestDataCollector(),
            new MemoryCollector(),
            new TimeDataCollector(),
            new ConfigCollector(),
        ];

        foreach ($collectors as $collector) {
            $debugBar->addCollector($collector);
        }

        self::assertSame(
            ['php', 'messages', 'request', 'memory', 'time', 'config'],
            array_keys($debugBar->getCollectors())
        );
    }

    public function testConfigCollectorCollectsJsonEncodableChangesUnderConfigName(): void
    {
        $collector = new ConfigCollector();

        $collector->set('debug_mode', true, 'core');
        $collector->set('debug_mode', false, 'local');

        $data = $collector->collect();

        self::assertSame('config', $collector->getName());
        self::assertArrayHasKey('debug_mode', $data);
        self::assertSame('local', $data['debug_mode'][0]['source']);
        self::assertSame('core', $data['debug_mode'][1]['source']);
        self::assertNotFalse(json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function testConfigCollectorKeepsOkayVariableListWidget(): void
    {
        $collector = new ConfigCollector();

        self::assertSame(
            'PhpDebugBar.Widgets.OkayVariableListWidget',
            $collector->getWidgets()['config']['widget']
        );
    }

    public function testTimeCollectorPreservesOkayTimelineWidgetAndMeasureMetadata(): void
    {
        $collector = new TimeDataCollector(microtime(true) - 1);

        $collector->startMeasure('module/init', 'Module init', 'modules', true);
        $collector->stopMeasure('module/init', ['Extension' => 'Trigger -> Class::method']);

        $data = $collector->collect();

        self::assertSame('PhpDebugBar.Widgets.OkayTimelineWidget', $collector->getWidgets()['timeline']['widget']);
        self::assertSame('module/init', $data['measures'][0]['name']);
        self::assertSame('Module init', $data['measures'][0]['label']);
        self::assertSame('modules', $data['measures'][0]['collector']);
        self::assertTrue($data['measures'][0]['aggregate']);
        self::assertSame('Trigger -> Class::method', $data['measures'][0]['params']['Extension']);
    }

    public function testQueryFormatterAcceptsBooleanBindings(): void
    {
        $formatter = new QueryFormatter();

        self::assertSame(
            'SELECT * FROM ok_products WHERE visible = 1 AND featured = 0',
            $formatter->formatSqlWithBindings(
                'SELECT * FROM ok_products WHERE visible = ? AND featured = ?',
                [true, false]
            )
        );
    }
}
