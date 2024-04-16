<?php

namespace Okay\Core\Modules\DTO;

class ModuleParamsDTO
{
    private string $version = '1.0.0';
    private string $okayVersion = '';
    private int $daysToExpire = -1;
    private bool $accessExpired = false;
    private string $addToCartUrl = '';
    private int $mathVersion;
    private string $vendorEmail = '';
    private string $vendorSite = '';
    /** @var ModificationDTO[] */
    private array $frontModifications = [];
    /** @var ModificationDTO[] */
    private array $backendModifications = [];
    private bool $isOfficial = false;
    private bool $isLicensed = false;

    /**
     * @param array $params
     * @return void
     *
     * Метод заповнює об'єкт даними
     */
    public function fromArray(array $params): void
    {
        if (isset($params['version'])) {
            $this->setVersion($params['version']);
        }
        if (isset($params['vendor']['email'])) {
            $this->setVendorEmail($params['vendor']['email']);
        }
        if (isset($params['vendor']['site'])) {
            $this->setVendorSite($params['vendor']['site']);
        }
        if (isset($params['Okay'])) {
            $this->setOkayVersion($params['Okay']);
        }
        if (isset($params['math_version'])) {
            $this->setMathVersion($params['math_version']);
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getDaysToExpire(): int
    {
        return $this->daysToExpire;
    }

    /**
     * @param int $daysToExpire
     */
    public function setDaysToExpire(int $daysToExpire): void
    {
        $this->daysToExpire = $daysToExpire;
    }

    /**
     * @return bool
     */
    public function isAccessExpired(): bool
    {
        return $this->accessExpired;
    }

    /**
     * @param bool $accessExpired
     */
    public function setAccessExpired(bool $accessExpired): void
    {
        $this->accessExpired = $accessExpired;
    }

    /**
     * @return string
     */
    public function getAddToCartUrl(): string
    {
        return $this->addToCartUrl;
    }

    /**
     * @param string $addToCartUrl
     */
    public function setAddToCartUrl(string $addToCartUrl): void
    {
        $this->addToCartUrl = $addToCartUrl;
    }

    /**
     * @return int
     */
    public function getMathVersion(): int
    {
        return $this->mathVersion;
    }

    /**
     * @param int $mathVersion
     */
    public function setMathVersion(int $mathVersion): void
    {
        $this->mathVersion = $mathVersion;
    }

    /**
     * @return string
     */
    public function getVendorEmail(): string
    {
        return $this->vendorEmail;
    }

    /**
     * @param string $vendorEmail
     */
    public function setVendorEmail(string $vendorEmail): void
    {
        $this->vendorEmail = $vendorEmail;
    }

    /**
     * @return string
     */
    public function getVendorSite(): string
    {
        return $this->vendorSite;
    }

    /**
     * @param string $vendorSite
     */
    public function setVendorSite(string $vendorSite): void
    {
        $this->vendorSite = $vendorSite;
    }

    /**
     * @return array
     */
    public function getFrontModifications(): array
    {
        return $this->frontModifications;
    }

    /**
     * @param ModificationDTO $modificationDTO
     */
    public function setFrontModification(ModificationDTO $modificationDTO): void
    {
        $this->frontModifications[] = $modificationDTO;
    }

    /**
     * @return array
     */
    public function getBackendModifications(): array
    {
        return $this->backendModifications;
    }

    /**
     * @param ModificationDTO $modificationDTO
     */
    public function setBackendModification(ModificationDTO $modificationDTO): void
    {
        $this->backendModifications[] = $modificationDTO;
    }

    /**
     * @return string
     */
    public function getOkayVersion(): string
    {
        return $this->okayVersion;
    }

    /**
     * @param string $okayVersion
     */
    public function setOkayVersion(string $okayVersion): void
    {
        $this->okayVersion = $okayVersion;
    }

    /**
     * @return bool
     */
    public function isOfficial(): bool
    {
        return $this->isOfficial;
    }

    /**
     * @param bool $isOfficial
     */
    public function setIsOfficial(bool $isOfficial): void
    {
        $this->isOfficial = $isOfficial;
    }

    /**
     * @return bool
     */
    public function isLicensed(): bool
    {
        return $this->isLicensed;
    }

    /**
     * @param bool $isLicensed
     */
    public function setIsLicensed(bool $isLicensed): void
    {
        $this->isLicensed = $isLicensed;
    }
}