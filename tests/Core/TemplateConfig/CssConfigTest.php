<?php

declare(strict_types=1);

namespace Core\TemplateConfig;

use Okay\Core\TemplateConfig\CssConfig;
use PHPUnit\Framework\TestCase;

final class CssConfigTest extends TestCase
{
    public function testUpdateCssVariablesDoesNotDuplicateGeneratedHeader(): void
    {
        $workspace = $this->createWorkspace();
        $settingsFile = $workspace . '/theme-settings.css';
        file_put_contents(
            $settingsFile,
            <<<'CSS'
            /**
            * Файл стилей для настройки шаблона.
            * Регистрировать этот файл для подключения в шаблоне не нужно
            */
            :root {
                --okay-button-color: #c5530c;
            }
            CSS
        );

        $config = new CssConfig($workspace, $settingsFile);
        $config->updateCssVariables(['--okay-button-color' => '#111']);
        $config->updateCssVariables(['--okay-button-color' => '#222']);

        $settingsContent = (string) file_get_contents($settingsFile);

        self::assertSame(
            1,
            substr_count($settingsContent, 'Файл стилей для настройки шаблона.')
        );
        self::assertStringContainsString('--okay-button-color: #222;', $settingsContent);
    }

    private function createWorkspace(): string
    {
        $workspace = sys_get_temp_dir() . '/okay-css-config-' . bin2hex(random_bytes(8));
        mkdir($workspace, 0777, true);

        return $workspace;
    }
}
