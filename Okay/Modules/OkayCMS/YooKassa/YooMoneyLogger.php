<?php


namespace Okay\Modules\OkayCMS\YooKassa;


class YooMoneyLogger
{
    const MESSAGE_TYPE = 3;

    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    private $isDebugEnabled;

    public function __construct($isDebugEnabled)
    {
        $this->isDebugEnabled = $isDebugEnabled;
    }

    public function info($message)
    {
        $this->log(self::LEVEL_INFO, $message);
    }

    public function error($message)
    {
        self::log(self::LEVEL_ERROR, $message);
    }

    public function warning($message)
    {
        self::log(self::LEVEL_ERROR, $message);
    }

    public function log($level, $message)
    {
        $filePath       = __DIR__.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.'ym-checkout-debug.log';
        if ($this->isDebugEnabled) {
            if (!file_exists($filePath)) {
                $dir = dirname($filePath);
                if (!is_writable($dir)) {
                    mkdir($dir);
                }
                touch($filePath);
                chmod($filePath, 0644);
            }

            $messageFormatted = self::formatMessage($level, $message);
            error_log($messageFormatted, self::MESSAGE_TYPE, $filePath);
        }
    }

    private function formatMessage($level, $message)
    {
        $date = date('Y-m-d H:i:s');

        return sprintf("[%s] [%s] Message: %s \r\n", $date, $level, $message);
    }
}