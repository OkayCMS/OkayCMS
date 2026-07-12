<?php

namespace Okay\Helpers;

interface GetListInterface
{
    /**
     * @param array<string, mixed> $filter
     * @param string|null $sortName
     * @param array<int, string>|false|null $excludedFields
     * @return array<int, object>
     * @throws \Exception
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null);

    public function getExcludeFields();
}
