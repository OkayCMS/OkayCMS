<?php

namespace Okay\Core\Modules;

class VersionControl
{
    public function versionCompare($version1, $version2): ?int
    {
        return version_compare($version1, $version2);
    }

    public function greaterThan($version1, $version2): bool
    {
        return $this->versionCompare($version1, $version2) === 1;
    }

    public function lessThan($version1, $version2): bool
    {
        return $this->versionCompare($version1, $version2) === -1;
    }

    public function equal($version1, $version2): bool
    {
        return $this->versionCompare($version1, $version2) === 0;
    }
}