<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\ServiceLocator;
use Okay\Core\Settings;

abstract class AbstractAiRequest
{
    protected ?int $entityId;
    protected ?string $name;
    protected Settings $settings;

    public function __construct(?int $entityId, ?string $name)
    {
        $this->entityId = $entityId;
        $this->name = $name;
        $SL = ServiceLocator::getInstance();
        $this->settings = $SL->getService(Settings::class);
    }

    abstract public function getRequestText(string $field): string;

    public function getAdditionalInfo(): string
    {
        return '';
    }

    protected function toPlainText(?string $html, int $maxLength = 1500): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim((string)$text);
        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) > $maxLength) {
            return rtrim(mb_substr($text, 0, $maxLength - 1)) . '…';
        }

        return $text;
    }

    /**
     * @param array<string, string> $sections
     */
    protected function buildContextBlock(array $sections): string
    {
        $lines = [];
        foreach ($sections as $label => $value) {
            $value = trim((string)$value);
            if ($value === '') {
                continue;
            }
            $lines[] = $label . ":\n" . $value;
        }

        if (empty($lines)) {
            return '';
        }

        return "Use only the facts below. Do not invent missing details.\n\n" . implode("\n\n", $lines);
    }
}
