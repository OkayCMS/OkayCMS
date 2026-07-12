<?php

declare(strict_types=1);

namespace Core\TemplateConfig;

use Okay\Core\TemplateConfig\Js;
use Okay\Core\TemplateConfig\JsConfig;
use PHPUnit\Framework\TestCase;

final class JsConfigTest extends TestCase
{
    public function testMinifyJsRemovesCommentsAndWhitespace(): void
    {
        $minified = JsConfig::minifyJs(
            <<<'JS'
            function sum(first, second) {
                // Keep the return value, remove this comment.
                return first + second;
            }
            JS
        );

        self::assertStringNotContainsString('Keep the return value', $minified);
        self::assertStringContainsString('function sum', $minified);
        self::assertStringContainsString('return first+second', $minified);
    }

    public function testCompileRegisteredCombinesAndMinifiesFiles(): void
    {
        $workspace = $this->createWorkspace();
        $sourceA = $this->writeFile($workspace, 'first.js', 'function first() { return 1; }');
        $sourceB = $this->writeFile($workspace, 'second.js', 'function second() { return 2; }');

        $config = new JsConfig();
        $config->register(new Js('first.js'), $sourceA);
        $config->register(new Js('second.js'), $sourceB);

        $compiled = $config->compileRegistered('head', $workspace . '/compiled/', 'theme');

        self::assertFileExists($compiled);
        self::assertStringStartsWith($workspace . '/compiled/theme.head.', $compiled);

        $content = (string) file_get_contents($compiled);
        self::assertStringContainsString('function first(){return 1;', $content);
        self::assertStringContainsString('function second(){return 2;', $content);
    }

    public function testCompileRegisteredIndividualMinifiesAndPreservesAttributes(): void
    {
        $workspace = $this->createWorkspace();
        $source = $this->writeFile($workspace, 'deferred.js', 'function deferred() { return true; }');

        $js = (new Js('deferred.js'))
            ->setIndividual(true)
            ->setDefer(true)
            ->setAttribute('type', 'module');

        $config = new JsConfig();
        $config->register($js, $source);

        $compiledBySource = $config->compileRegisteredIndividual('head', $workspace . '/compiled/', 'theme');

        self::assertArrayHasKey($source, $compiledBySource);

        $compiled = $compiledBySource[$source];
        self::assertFileExists($compiled);
        self::assertTrue($config->hasDefer($compiled));
        self::assertSame(['type' => 'module'], $config->getAttributes($compiled));
        self::assertStringContainsString('function deferred(){return true;', (string) file_get_contents($compiled));
    }

    private function createWorkspace(): string
    {
        $workspace = sys_get_temp_dir() . '/okay-js-config-' . bin2hex(random_bytes(8));
        mkdir($workspace . '/compiled', 0777, true);

        return $workspace;
    }

    private function writeFile(string $workspace, string $filename, string $content): string
    {
        $path = $workspace . '/' . $filename;
        file_put_contents($path, $content);

        return $path;
    }
}
