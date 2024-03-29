<?php

namespace Okay\Core\Modules;

use Okay\Core\Modules\DTO\LicenseDTO;
use Okay\Core\Request;

class LicenseStorage
{
    private string $compileCodeDir;

    public function __construct(string $compileCodeDir)
    {
        $this->compileCodeDir = $compileCodeDir;

        if (!is_dir($this->compileCodeDir)) {
            mkdir($this->compileCodeDir, 0777, true);
        }
    }

    public function saveLicense(LicenseDTO $licenseDTO)
    {
        file_put_contents(
            $this->getLicenseFilename(),
            serialize($licenseDTO),
            LOCK_EX
        );
    }

    public function getLicense(): ?LicenseDTO
    {
        $licenseFilename = $this->getLicenseFilename();
        if (!is_file($licenseFilename)) {
            return null;
        }
        $licenseContent = file_get_contents($licenseFilename);
        if (empty($licenseContent)
            || strpos($licenseContent, "\n") !== false
            || strpos($licenseContent, "\r") !== false
        ) {
            return null;
        }

        $licenseDTO = @unserialize($licenseContent);

        if (!is_object($licenseDTO)) {
            return null;
        }

        if (!$licenseDTO instanceof LicenseDTO) {
            return null;
        }
        return $licenseDTO;
    }

    private function getLicenseFilename(): string
    {
        return sprintf('%s%s.license',
            $this->compileCodeDir,
            md5(Request::getDomain())
        );
    }
}