<?php

namespace Okay\Core\Modules\DTO;

class ModuleParamsDTO
{
    private $version = '1.0.0';
    private $okayVersion;
    private $daysToExpire = -1;
    private $accessExpired = false;
    private $addToCartUrl = '';
    private $mathVersion ;
    private $vendorEmail = '';
    private $vendorSite = '';
    private $frontModifications = [];
    private $backendModifications = [];

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
        if (isset($params['modifications']['backend'])) {
            $this->setBackendModifications($params['modifications']['backend']);
        }
        if (isset($params['modifications']['front'])) {
            $this->setFrontModifications($params['modifications']['front']);
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
     * @param array $frontModifications
     */
    public function setFrontModifications(array $frontModifications): void
    {
        $this->frontModifications = $frontModifications;
    }

    /**
     * @return array
     */
    public function getBackendModifications(): array
    {
        return $this->backendModifications;
    }

    /**
     * @param array $backendModifications
     */
    public function setBackendModifications(array $backendModifications): void
    {
        $this->backendModifications = $backendModifications;
    }

    /**
     * @return mixed
     */
    public function getOkayVersion()
    {
        return $this->okayVersion;
    }

    /**
     * @param mixed $okayVersion
     */
    public function setOkayVersion($okayVersion): void
    {
        $this->okayVersion = $okayVersion;
    }
}