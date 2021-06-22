<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;

class JsonLdText extends Modifier
{
    protected $tag = 'json_ld_text';

    public function run(string $str) : string
    {
        $str = strtr($str, [
            "\\" => "\\\\",
            "\n" => " ",
        ]);
        return trim(htmlspecialchars(strip_tags($str)));
    }
}