<?php

namespace Okay\Modules\OkayCMS\Banners\VO;

class RestoreBackupErrorVO
{
    public const UNZIP_ERROR = 1;
    public const WRONG_CONFIG_FILE = 2;
    public const GROUP_ALREADY_EXISTS = 3;

    private string $errorLangDirective;

    private array $errorTextParams;

    public function __construct(int $errorCode, array $errorTextParams = [])
    {
        switch ($errorCode) {
            case self::UNZIP_ERROR:
                $this->errorLangDirective = 'banners_backup_error_unzip';
                break;
            case self::WRONG_CONFIG_FILE:
                $this->errorLangDirective = 'banners_backup_error_wrong_config';
                break;
            case self::GROUP_ALREADY_EXISTS:
                $this->errorLangDirective = 'banners_backup_error_group_already_exists';
                break;
        }
        $this->errorTextParams = $errorTextParams;
    }

    /**
     * @return array
     */
    public function getErrorTextParams(): array
    {
        return $this->errorTextParams;
    }

    /**
     * @return string
     */
    public function getErrorLangDirective(): string
    {
        return $this->errorLangDirective;
    }
}