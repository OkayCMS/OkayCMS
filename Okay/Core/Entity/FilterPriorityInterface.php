<?php

namespace Okay\Core\Entity;

interface FilterPriorityInterface
{
    /**
     * @param $filterName string
     * @return $this
     */
    public function addHighPriority($filterName);

    /**
     * @param $filterName string
     * @return $this
     */
    public function addLowPriority($filterName);

    /**
     * @param $filterName string
     * @return $this
     */
    public function removeHighPriority($filterName);

    /**
     * @param $filterName string
     * @return $this
     */
    public function removeLowPriority($filterName);

    /**
     * @return $this
     */
    public function resetPriority();

    /**
     * @param array<string, mixed> $filter
     * @return array<string, mixed>
     */
    public function orderFilterByPriority(array $filter = []);
}
