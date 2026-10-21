<?php


namespace Okay\Core;


use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class Phone
{
    
    private $settings;
    
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getPhoneExample(): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneExample = '';
        if ($this->settings->get('phone_default_region')) {
            switch ($this->settings->get('phone_default_region')) {
                case 'UA' :
                    $phoneExample = '+380442903833';
                    break;
                case 'RU' :
                    $phoneExample = '+74996482047';
                    break;
                default:
                    $phoneExample = $phoneUtil->getExampleNumber($this->settings->get('phone_default_region'));
                    
            }
            $phoneExample = self::format($phoneExample, PhoneNumberFormat::INTERNATIONAL);
        }
        return $phoneExample;
    }
    
    /**
     * Метод подготавливает номер телефона для сохранения в базу, в базе они хранятся в стандарте E164
     * 
     * @param $phoneNumber
     * @return string
     * @throws NumberParseException
     */
    public static function toSave($phoneNumber): ?string
    {
        try {
            return self::format($phoneNumber, PhoneNumberFormat::E164);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Метод очищает телефон от всех лишних символов
     * 
     * @param $phoneNumber
     * @return string
     */
    public static function clear($phoneNumber): string
    {
        return substr((string) preg_replace('~[^0-9.+]~', '', (string) $phoneNumber), 0, PhoneNumberUtil::MAX_LENGTH_FOR_NSN);
    }

    /**
     * Проверяет валидный ли номер телефона с учетом данных настроек
     * 
     * @param $phoneNumber
     * @return bool
     * @throws NumberParseException
     */
    public static function isValid($phoneNumber): bool
    {
        $wrongPrefixes = [
            '+0',
            '+89',
        ];
        
        foreach ($wrongPrefixes as $prefix) {
            if (strpos((string) $phoneNumber, $prefix) === 0) {
                $phoneNumber = ltrim((string) $phoneNumber, '+');
            }
        }
        
        if (empty($phoneNumber)) {
            return false;
        }
        
        if (!PhoneNumberUtil::isViablePhoneNumber($phoneNumber)) {
            return false;
        }
        
        if (($len = mb_strlen(trim((string) $phoneNumber, '+'))) < 5 || $len > 20) {
            return false;
        }
        
        $SL = ServiceLocator::getInstance();
        /** @var Settings $settings */
        $settings = $SL->getService(Settings::class);

        $defaultRegion = $settings->get('phone_default_region');
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneObject = $phoneUtil->parse($phoneNumber, $defaultRegion);
            return $phoneUtil->isValidNumber($phoneObject);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Метод форматирует телефон в соответствии с настройками
     * 
     * @param $phoneNumber
     * @param null $numberFormat
     * @return string
     * @throws NumberParseException
     */
    public static function format($phoneNumber, $numberFormat = null): string
    {
        if (substr((string) $phoneNumber, 0, 2) == '+0') {
            $phoneNumber = substr((string) $phoneNumber, 1);
        }

        if (!$phoneNumber = self::clear($phoneNumber)) {
            return '';
        }

        $SL = ServiceLocator::getInstance();
        $phoneUtil = PhoneNumberUtil::getInstance();

        /** @var Settings $settings */
        $settings = $SL->getService(Settings::class);

        $defaultRegion = $settings->get('phone_default_region');
        
        if ($numberFormat === null) {
            $numberFormat = $settings->get('phone_default_format');
        }
        $numberFormat = self::normalizeFormat($numberFormat);

        $phoneObject = $phoneUtil->parse($phoneNumber, $defaultRegion);
        return $phoneUtil->format($phoneObject, $numberFormat);
    }

    /**
     * @param int|PhoneNumberFormat $numberFormat
     * @return int|PhoneNumberFormat
     */
    private static function normalizeFormat($numberFormat)
    {
        if (function_exists('enum_exists') && enum_exists(PhoneNumberFormat::class)) {
            if (!$numberFormat instanceof PhoneNumberFormat) {
                $numberFormat = PhoneNumberFormat::from((int) $numberFormat);
            }
            return $numberFormat;
        }

        return (int) $numberFormat;
    }

    /**
     * @param int|PhoneNumberFormat $numberFormat
     */
    public static function formatValue($numberFormat): int
    {
        return is_int($numberFormat) ? $numberFormat : $numberFormat->value;
    }
}