<?php

namespace Okay\Core\DebugBar\DataCollectors;

use DebugBar\DataCollector\TimeDataCollector as LibTimeDataCollector;
use DebugBar\DebugBarException;

class TimeDataCollector extends LibTimeDataCollector
{
    public function startMeasure(string $name, ?string $label = null, ?string $collector = null, mixed $group = null): void
    {
        $start = microtime(true);
        $this->startedMeasures[$name] = array(
            'name' => $name,
            'label' => $label ?: $name,
            'start' => $start,
            'memory' => $this->memoryMeasure ? memory_get_usage(false) : null,
            'collector' => $collector,
            'group' => is_string($group) ? $group : null,
            'aggregate' => is_bool($group) ? $group : false
        );
    }

    /**
     * @param array<string, mixed> $params
     */
    public function stopMeasure(string $name, array $params = array()): void
    {
        $end = microtime(true);
        if (!$this->hasStartedMeasure($name)) {
            throw new DebugBarException("Failed stopping measure '$name' because it hasn't been started");
        }
        if (!is_null($this->startedMeasures[$name]['memory'])) {
            $params['memoryUsage'] = memory_get_usage(false) - $this->startedMeasures[$name]['memory'];
        }
        $this->addMeasure(
            $this->startedMeasures[$name]['label'],
            $this->startedMeasures[$name]['start'],
            $end,
            $params,
            $this->startedMeasures[$name]['collector'],
            $this->startedMeasures[$name]['group'],
            $this->startedMeasures[$name]['name'],
            $this->startedMeasures[$name]['aggregate']
        );
        unset($this->startedMeasures[$name]);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function addMeasure(
        string $label,
        ?float $start = null,
        ?float $end = null,
        array $params = array(),
        ?string $collector = null,
        mixed $group = null,
        ?string $name = null,
        bool $aggregate = false
    ): void {
        $start ??= microtime(true);
        $end ??= $start;
        if (isset($params['memoryUsage'])) {
            $memory = $this->memoryMeasure ? $params['memoryUsage'] : 0;
            unset($params['memoryUsage']);
        }

        $this->measures[] = array(
            'name' => $name ?? $label,
            'label' => $label,
            'start' => $start,
            'relative_start' => $start - $this->requestStartTime,
            'end' => $end,
            'relative_end' => $end - $this->requestEndTime,
            'duration' => $end - $start,
            'duration_str' => $this->getDataFormatter()->formatDuration($end - $start),
            'memory' => $memory ?? 0,
            'memory_str' => $this->getDataFormatter()->formatBytes($memory ?? 0),
            'params' => $params,
            'collector' => $collector,
            'group' => is_string($group) ? $group : null,
            'aggregate' => $aggregate
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getWidgets(): array
    {
        return array(
            "time" => array(
                "icon" => "clock",
                "tooltip" => "Request Duration",
                "map" => "time.duration_str",
                "link" => "timeline",
                "default" => "'0ms'"
            ),
            "timeline" => array(
                "icon" => "chart-infographic",
                "widget" => "PhpDebugBar.Widgets.OkayTimelineWidget",
                "map" => "time",
                "default" => "{}"
            )
        );
    }
}
