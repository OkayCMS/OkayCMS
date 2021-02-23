<?php


namespace TplMod;


use Okay\Core\TplMod\Parser;

class TplModParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param $string
     * @param $expectedResult
     * @dataProvider parseStringDataProvider
     */
    public function testParseString($string, $expectedResult)
    {
        $parser = new Parser();
        $actualResult = $parser->parseString($string);
        
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function parseStringDataProvider()
    {
        require_once(__DIR__.'/../../vendor/autoload.php');
        
        return [
            [// Smarty комменты
                '{*Smarty comment*}<div class="test">...</div>', 
                [
                    [
                        '{*Smarty comment*}<div class="test">...</div>',
                        'Smarty comment',
                        '<div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// html комменты
                '<!--html comment--><div class="test">...</div>',
                [
                    [],
                    [
                        '<!--html comment--><div class="test">...</div>',
                        'html comment',
                        '<div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// Html тег, который формируется с условиями
                '<{if $c->id == $category->id}b{else}a{/if} class="filter__catalog_link>...</{if $c->id == $category->id}b{else}a{/if}><div class="test">...</div>', 
                [
                    [],
                    [],
                    [
                        '<{if $c->id == $category->id}b{else}a{/if} class="filter__catalog_link>...</{if $c->id == $category->id}b{else}a{/if}><div class="test">...</div>',
                        '<{if $c->id == $category->id}b{else}a{/if} class="filter__catalog_link>',
                        '{if $c->id == $category->id}b{else}a{/if}',
                        '...</{if $c->id == $category->id}b{else}a{/if}><div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// Пустой html тег
                '<div class="some_class"></div><div class="test">...</div>', 
                [
                    [],
                    [],
                    [
                        '<div class="some_class"></div><div class="test">...</div>',
                        '<div class="some_class">',
                        'div',
                        '</div><div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// html тег со Smarty внутри
                '<div class="some_class">{$foo}</div><div class="test">...</div>', 
                [
                    [],
                    [],
                    [
                        '<div class="some_class">{$foo}</div><div class="test">...</div>',
                        '<div class="some_class">',
                        'div',
                        '{$foo}</div><div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// Html тег, в котором присутствует сложный синтаксис Smarty
                '<a class="filter__link{if $smarty.get.{$f@key} && in_array($fv->translit,$smarty.get.{$f@key},true)} checked{/if}" href="{$furl}">...</a><div class="test">...</div>', 
                [
                    [],
                    [],
                    [
                        '<a class="filter__link{if $smarty.get.{$f@key} && in_array($fv->translit,$smarty.get.{$f@key},true)} checked{/if}" href="{$furl}">...</a><div class="test">...</div>',
                        '<a class="filter__link{if $smarty.get.{$f@key} && in_array($fv->translit,$smarty.get.{$f@key},true)} checked{/if}" href="{$furl}">',
                        'a',
                        '...</a><div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// стандартный HTML тег
                '<span id="test">...</span><div class="test">...</div>', 
                [
                    [],
                    [],
                    [
                        '<span id="test">...</span><div class="test">...</div>',
                        '<span id="test">',
                        'span',
                        '...</span><div class="test">...</div>',
                    ],
                    [],
                    [],
                    [],
                    [],
                ]
            ],
            [// текст smarty синтаксис с вложенными тегами, но парсится как текст
                '{$var = {func}}<div class="test">...</div>', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '{$var = {func}}<div class="test">...</div>',
                        '{$var = {func}}',
                        '<div class="test">...</div>',
                    ],
                ]
            ],
            [// текст smarty синтаксис с вложенными тегами, но парсится как текст
                '{foreachelse}<div class="test2">...</div>{/foreach}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '{foreachelse}<div class="test2">...</div>{/foreach}',
                        '{foreachelse}',
                        '<div class="test2">...</div>{/foreach}',
                    ],
                ]
            ],
            [// текст smarty с Smarty блочными элементами
                '{$f_count = 0}{foreach $f->features_values as $fv}{$f_count = $f_count+1}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '{$f_count = 0}{foreach $f->features_values as $fv}{$f_count = $f_count+1}',
                        '{$f_count = 0}',
                        '{foreach $f->features_values as $fv}{$f_count = $f_count+1}',
                    ],
                ]
            ],
            [// текст закрывающими тегами
                '</div>{/foreach}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '</div>{/foreach}',
                        '',
                        '</div>{/foreach}',
                    ],
                ]
            ],
            [// парс DOCTYPE
                '<!DOCTYPE html> <html prefix="og: http://ogp.me/ns#"><head></head><body></body></html>', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '<!DOCTYPE html> <html prefix="og: http://ogp.me/ns#"><head></head><body></body></html>',
                        '<!DOCTYPE html> ',
                        '<html prefix="og: http://ogp.me/ns#"><head></head><body></body></html>',
                    ],
                ]
            ],
            [// smarty цикл
                '{foreach $array as $item}<div class="test">...</div>{/foreach}', 
                [
                    [],
                    [],
                    [],
                    [
                        '{foreach $array as $item}<div class="test">...</div>{/foreach}',
                        '{foreach $array as $item}',
                        'foreach',
                        '<div class="test">...</div>{/foreach}',
                    ],
                    [],
                    [],
                    [],
                ]
            ],
            [// smarty цикл
                '{foreach from=$myArray item=foo}<div class="test">...</div>{/foreach}', 
                [
                    [],
                    [],
                    [],
                    [
                        '{foreach from=$myArray item=foo}<div class="test">...</div>{/foreach}',
                        '{foreach from=$myArray item=foo}',
                        'foreach',
                        '<div class="test">...</div>{/foreach}',
                    ],
                    [],
                    [],
                    [],
                ]
            ],
            [// smarty цикл
                '{foreach from=$myArray item=foo}<div class="test">...</div>{foreachelse}<div class="test2">...</div>{/foreach}', 
                [
                    [],
                    [],
                    [],
                    [
                        '{foreach from=$myArray item=foo}<div class="test">...</div>{foreachelse}<div class="test2">...</div>{/foreach}',
                        '{foreach from=$myArray item=foo}',
                        'foreach',
                        '<div class="test">...</div>{foreachelse}<div class="test2">...</div>{/foreach}',
                    ],
                    [],
                    [],
                    [],
                ]
            ],
            [// smarty функция
                '{function name="func"}<div class="test">...</div>{/function}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [
                        '{function name="func"}<div class="test">...</div>{/function}',
                        '{function name="func"}',
                        'function',
                        '<div class="test">...</div>{/function}',
                    ],
                    [],
                    [],
                ]
            ],
            [// smarty условие
                '{if !empty($array)}<div class="test">...</div>{/if}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '{if !empty($array)}<div class="test">...</div>{/if}',
                        '{if !empty($array)}',
                        'if',
                        '<div class="test">...</div>{/if}',
                    ],
                    [],
                ]
            ],
            [// smarty условие
                '{if !empty({$array})}<div class="test">...</div>{/if}', 
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [
                        '{if !empty({$array})}<div class="test">...</div>{/if}',
                        '{if !empty({$array})}',
                        'if',
                        '<div class="test">...</div>{/if}',
                    ],
                    [],
                ]
            ],
        ];
    }
    
}