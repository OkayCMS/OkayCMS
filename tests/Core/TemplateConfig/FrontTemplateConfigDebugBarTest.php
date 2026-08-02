<?php

declare(strict_types=1);

namespace Core\TemplateConfig;

use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DebugBar as LibDebugBar;
use Okay\Core\DebugBar\DebugBar;
use Okay\Core\TemplateConfig\CssConfig;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\TemplateConfig\JsConfig;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class FrontTemplateConfigDebugBarTest extends TestCase
{
    protected function tearDown(): void
    {
        $debugBar = new ReflectionProperty(DebugBar::class, 'debugBar');
        $debugBar->setValue(null, null);

        parent::tearDown();
    }

    public function testRegisterDebugBarFilesAddsLibraryAssetsAndOkayWidgetScript(): void
    {
        $libDebugBar = new LibDebugBar();
        $libDebugBar->addCollector(new MemoryCollector());

        $debugBar = new ReflectionProperty(DebugBar::class, 'debugBar');
        $debugBar->setValue(null, $libDebugBar);

        $workspace = $this->createWorkspace();
        $config = $this->newFrontTemplateConfig($workspace);

        $registerDebugBarFiles = new ReflectionMethod(FrontTemplateConfig::class, 'registerDebugBarFiles');
        $registerDebugBarFiles->invoke($config);

        $cssFiles = $this->readNestedValues($this->readProperty($config, 'cssConfig'), 'individualCss');
        $jsFiles = $this->readNestedValues($this->readProperty($config, 'jsConfig'), 'individualJs');

        self::assertNotEmpty($cssFiles);
        self::assertNotEmpty($jsFiles);
        self::assertContains('Okay/Core/DebugBar/Resources/js/widgets.js', $jsFiles);
        self::assertSame($cssFiles, array_filter($cssFiles, 'is_string'));
        self::assertSame($jsFiles, array_filter($jsFiles, 'is_string'));
    }

    public function testCssCompilationPreservesDebugBarToolbarStyles(): void
    {
        $workspace = $this->createWorkspace();
        $debugBarCss = 'vendor/php-debugbar/php-debugbar/resources/dist/debugbar.min.css';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        $cssConfig = new CssConfig($workspace, $workspace . '/settings.css');
        $compiledFile = $cssConfig->compileIndividual($debugBarCss, $workspace . '/css/', 'test_theme');

        $compiledCss = file_get_contents($compiledFile);

        self::assertIsString($compiledCss);
        self::assertStringContainsString('div.phpdebugbar-header', $compiledCss);
        self::assertStringContainsString('a.phpdebugbar-tab', $compiledCss);
        self::assertGreaterThan(70000, strlen($compiledCss));
    }

    private function newFrontTemplateConfig(string $workspace): FrontTemplateConfig
    {
        $reflection = new ReflectionClass(FrontTemplateConfig::class);
        $config = $reflection->newInstanceWithoutConstructor();

        $this->writeProperty($config, 'theme', 'test_theme');
        $this->writeProperty($config, 'adminTheme', '');
        $this->writeProperty($config, 'adminThemeManagers', []);
        $this->writeProperty($config, 'rootDir', $workspace);
        $this->writeProperty($config, 'compileCssDir', '/css/');
        $this->writeProperty($config, 'compileJsDir', '/js/');
        $this->writeProperty($config, 'cssConfig', new CssConfig($workspace, $workspace . '/settings.css'));
        $this->writeProperty($config, 'jsConfig', new JsConfig());

        return $config;
    }

    private function createWorkspace(): string
    {
        $workspace = sys_get_temp_dir() . '/okay-debugbar-assets-' . bin2hex(random_bytes(8));
        mkdir($workspace . '/css', 0777, true);
        mkdir($workspace . '/js', 0777, true);

        return $workspace;
    }

    /**
     * @return list<string>
     */
    private function readNestedValues(object $object, string $propertyName): array
    {
        $values = [];
        $property = new ReflectionProperty($object, $propertyName);

        foreach ($property->getValue($object) as $group) {
            foreach ($group as $value) {
                $values[] = $value;
            }
        }

        return $values;
    }

    private function readProperty(object $object, string $propertyName): mixed
    {
        $property = new ReflectionProperty($object, $propertyName);

        return $property->getValue($object);
    }

    private function writeProperty(object $object, string $propertyName, mixed $value): void
    {
        $property = new ReflectionProperty($object, $propertyName);
        $property->setValue($object, $value);
    }
}
