<?php

namespace Okay\Core\Modules\DTO;

class LicenseDTO
{
    /** @var array<int, string>|null */
    private ?array $modulesLicenses = null;

    /** @var array<int, string> */
    private array $officialModules = [];

    private ?string $templateLicense = null;

    private bool $isOfficialTemplate = false;

    /**
     * @return array<int, string>|null
     */
    public function getModulesLicenses(): ?array
    {
        return $this->modulesLicenses;
    }

    /**
     * @param array<int, string> $modulesLicenses
     */
    public function setModulesLicenses(array $modulesLicenses): void
    {
        $this->modulesLicenses = $modulesLicenses;
    }

    /**
     * @return array<int, string>
     */
    public function getOfficialModules(): array
    {
        return $this->officialModules;
    }

    /**
     * @param array<int, string> $officialModules
     */
    public function setOfficialModules(array $officialModules): void
    {
        $this->officialModules = $officialModules;
    }

    /**
     * @return string|null
     */
    public function getTemplateLicense(): ?string
    {
        return $this->templateLicense;
    }

    /**
     * @param string $templateLicense
     */
    public function setTemplateLicense(string $templateLicense): void
    {
        $this->templateLicense = $templateLicense;
    }

    /**
     * @return bool
     */
    public function isOfficialTemplate(): bool
    {
        return $this->isOfficialTemplate;
    }

    /**
     * @param bool $isOfficialTemplate
     */
    public function setIsOfficialTemplate(bool $isOfficialTemplate): void
    {
        $this->isOfficialTemplate = $isOfficialTemplate;
    }
}
