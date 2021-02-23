<?php


namespace Okay\Core;


class JsSocial
{

    /**
     * Домен некоторых соц. сетей не соответствует стилям font-awesome, для них сделаны эти алиасы
     * 
     * @var string[] 
     */
    private static $socialAliases = [
        "ok" => 'odnoklassniki',
    ];

    private $jsSocials = [
        "email",
        "twitter",
        "facebook",
        "googleplus",
        "linkedin",
        "pinterest",
        "stumbleupon",
        "pocket",
        "whatsapp",
        "viber",
        "messenger",
        "telegram",
        "line",
        "odnoklassniki",
        "vkontakte",
    ];
    
    private $customJsSocials = [
        "odnoklassniki" => [
            "label" => "ok",
            "logo" => "fa fa-odnoklassniki",
            "shareUrl" => "https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl={url}&title={title}",
        ],
    ];
    
    public function getSocials()
    {
        return $this->jsSocials;
    }
    
    public function getCustomSocials()
    {
        return $this->customJsSocials;
    }
    
    public static function getSocialDomain($link)
    {
        $socialDomain = preg_replace('~^(https?://)?(www\.)?([^.]+)?\..*$~', '$3', $link);
        
        if (isset(self::$socialAliases[$socialDomain])) {
            return self::$socialAliases[$socialDomain];
        }
        return $socialDomain;
    }
    
}