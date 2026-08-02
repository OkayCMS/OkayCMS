<?php

namespace Okay\Core\Adapters\Response;

use Okay\Core\DebugBar\DebugBar;

class Xml extends AbstractResponse
{
    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/xml; charset=UTF-8',
        ];
    }

    public function send($content)
    {
        // XML фіди використовують sendStream(), який викликає send() багато разів
        // Викликаємо stackData() тільки один раз для всього фіду, якщо це не фід
        static $stackDataCalled = false;
        if (!$stackDataCalled && self::shouldCallStackData()) {
            DebugBar::stackData();
            $stackDataCalled = true;
        }
        print implode('', $content);
    }
}
