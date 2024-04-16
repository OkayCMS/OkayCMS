<?php

namespace Okay\Core\Modules\DTO;

class LicenseDTO
{
    private ?array $modulesLicenses = null;

    private array $officialModules = [];

    private ?string $templateLicense = null;

    private bool $isOfficialTemplate = false;

    /**
     * @return array|null
     */
    public function getModulesLicenses(): ?array
    {
        return $this->modulesLicenses;
    }

    /**
     * @param array $modulesLicenses
     */
    public function setModulesLicenses(array $modulesLicenses): void
    {
        $this->modulesLicenses = $modulesLicenses;
    }

    /**
     * @return array
     */
    public function getOfficialModules(): array
    {
        return $this->officialModules;
    }

    /**
     * @param array $officialModules
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