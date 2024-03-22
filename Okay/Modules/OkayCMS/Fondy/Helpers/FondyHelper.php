<?php


namespace Okay\Modules\OkayCMS\Fondy\Helpers;


class FondyHelper
{
    const ORDER_SEPARATOR = '#';
    const SIGNATURE_SEPARATOR = '|';
    const ORDER_APPROVED = 'approved';

    public static function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);
        $str = $password;
        foreach ($data as $k => $v) {
            $str .= self::SIGNATURE_SEPARATOR . $v;
        }
        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }
    
    public static function isPaymentValid($oplataSettings, $response)
    {
        if ($oplataSettings['merchant'] != $response['merchant_id']) {
            return 'An error has occurred during payment. Merchant data is incorrect.';
        }

        $responseSignature = $response['signature'];
        if (isset($response['response_signature_string'])){
            unset($response['response_signature_string']);
        }
        if (isset($response['signature'])){
            unset($response['signature']);
        }
        if (self::getSignature($response, $oplataSettings['secretkey']) != $responseSignature) {
            return 'An error has occurred during payment. Signature is not valid.';
        }
        return true;
    }
}