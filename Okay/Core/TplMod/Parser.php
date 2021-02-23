<?php


namespace Okay\Core\TplMod;


use Okay\Core\TplMod\Nodes\BaseNode;
use Okay\Core\TplMod\Nodes\HtmlCommentNode;
use Okay\Core\TplMod\Nodes\HtmlNode;
use Okay\Core\TplMod\Nodes\SmartyCommentNode;
use Okay\Core\TplMod\Nodes\SmartyForeachNode;
use Okay\Core\TplMod\Nodes\SmartyFunctionNode;
use Okay\Core\TplMod\Nodes\SmartyIfNode;
use Okay\Core\TplMod\Nodes\TextNode;

class Parser
{
    private $selfClosing = [
        'area',
        'base',
        'basefont',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'spacer',
        'track',
        'wbr'
    ];
    
    private $string;
    
    public function parse($string)
    {
        $this->string = $string;
        $domNode = new BaseNode('document');
        
        return $this->parseLevel($domNode);
    }
    
    public function parseString($string)
    {
        $matchesHtml = [];
        $matchesSmartyComment = [];
        $matchesSmartyForeach = [];
        $matchesSmartyFunction = [];
        $matchesSmartyIf = [];
        $matchesHtmlComment = [];
        $matchesText = [];
        
        if (preg_match('~^\s*{\*(.*?)\*}(.*)?~is', $string, $matchesSmartyComment)
            || preg_match('~^\s*<!--(.*?)-->(.*)?~is', $string, $matchesHtmlComment)
            || preg_match('~^\s*(<((?:{if\s.+?})?[a-z0-9]+(?:{elseif\s.+?}[a-z0-9]+)?(?:{else}[a-z0-9]+)?(?:{/if})?)(?:[^>]*?(?:{.*?(?:{.+?}.*?)?})?[^>]*?)*?>)+?(.*)?~is', $string, $matchesHtml)
            || preg_match('~^\s*({(foreach)\s.*?(?:{.+?}.*?)*})+(.*)?~is', $string, $matchesSmartyForeach)
            || preg_match('~^\s*({(function)\s.*?(?:{.+?}.*?)*})+(.*)?~is', $string, $matchesSmartyFunction)
            || preg_match('~^\s*({(if)\s.*?(?:{.+?}.*?)*})+(.*)?~is', $string, $matchesSmartyIf)
            || preg_match('~^\s*((?:<!DOCTYPE.*?>)?.*?)(<.*|{foreach\s.*|{function\s.*|{if\s.*|{/foreach}.*|{/function}.*|{/if}.*)*$~is', $string, $matchesText)) {
            return [
                $matchesSmartyComment,
                $matchesHtmlComment,
                $matchesHtml,
                $matchesSmartyForeach,
                $matchesSmartyFunction,
                $matchesSmartyIf,
                $matchesText
            ];
        }
        return false;
    }
    
    private function parseLevel(BaseNode $parentNode, $parentOpenTagName = '')
    {
        while (list($matchesSmartyComment,
            $matchesHtmlComment,
            $matchesHtml,
            $matchesSmartyForeach,
            $matchesSmartyFunction,
            $matchesSmartyIf,
            $matchesText) = $this->parseString($this->string)) {

            if (!empty($matchesText) ) {
                if (mb_strlen(trim($matchesText[1])) == 0) {
                    break;
                }
                
                $this->string = isset($matchesText[2]) ? $matchesText[2] : '';
                $content = trim($matchesText[1]);
                if (mb_strlen($content) > 0) {
                    $node = new TextNode($content);
                }
            } elseif (!empty($matchesHtmlComment)) {
                $this->string = $matchesHtmlComment[2];
                $node = new HtmlCommentNode('<!--' . $matchesHtmlComment[1] . '-->');
            } elseif (!empty($matchesSmartyComment)) {
                $this->string = $matchesSmartyComment[2];
                $node = new SmartyCommentNode('{*' . $matchesSmartyComment[1] . '*}');
            } elseif (!empty($matchesHtml)) {
                $openTagName = $matchesHtml[2];
                
                // Экранируем символы, которые будут не правильно поняты
                $openTagName = strtr($openTagName, [
                    '$' => '\$',
                ]);
                
                $openTag = $matchesHtml[1];
                $this->string = $matchesHtml[3];

                if (in_array($openTagName, $this->selfClosing)) {
                    $node = new HtmlNode($openTag, false);
                } else {
                    $node = new HtmlNode($openTag);
                }
                
                if (mb_strtolower($openTagName) == 'script') { 
                    $matchesScript = [];
                    preg_match('~^(.*?)(</' . $openTagName . '>.*)$~is', $this->string, $matchesScript);
                    if (!empty(trim($matchesScript[1]))) {
                        $childNode = new TextNode(trim($matchesScript[1]));
                        $node->append($childNode);
                    }
                    $this->string = $matchesScript[2];
                    // todo ниже добавил регулярку, смотреть мож убрать эту часть условия
                } elseif (/*!preg_match('~^\s*(</' . $openTagName . '>)(.*)$~is', $this->string) && */!in_array(mb_strtolower($openTagName), $this->selfClosing)) {
                    $this->parseLevel($node, $openTagName);
                }

                $matchesClose = [];
                if (!empty($openTagName) && preg_match('~^\s*(</' . $openTagName . '>)(.*)$~is', $this->string, $matchesClose)) {
                    $this->string = $matchesClose[2];
                    $node->setCloseTag($matchesClose[1]);
                }
            } elseif (!empty($matchesSmartyForeach)) {
                $openTagName = $matchesSmartyForeach[2];
                $openTag = $matchesSmartyForeach[1];
                $this->string = $matchesSmartyForeach[3];

                $node = new SmartyForeachNode($openTag);
                $this->parseLevel($node, $openTagName);
                $matchesClose = [];
                
                if (!empty($openTagName) && preg_match('~^\s*({/' . $openTagName . '})(.*)$~is', $this->string, $matchesClose)) {
                    $this->string = $matchesClose[2];
                    $node->setCloseTag($matchesClose[1]);
                }
            } elseif (!empty($matchesSmartyFunction)) {
                $openTagName = $matchesSmartyFunction[2];
                $openTag = $matchesSmartyFunction[1];
                $this->string = $matchesSmartyFunction[3];

                $node = new SmartyFunctionNode($openTag);
                $this->parseLevel($node, $openTagName);
                $matchesClose = [];
                if (!empty($openTagName) && preg_match('~^\s*({/' . $openTagName . '})(.*)$~is', $this->string, $matchesClose)) {
                    $this->string = $matchesClose[2];
                    $node->setCloseTag($matchesClose[1]);
                }
            } elseif (!empty($matchesSmartyIf)) {
                $openTagName = $matchesSmartyIf[2];
                $openTag = $matchesSmartyIf[1];
                $this->string = $matchesSmartyIf[3];

                $node = new SmartyIfNode($openTag);
                $this->parseLevel($node, $openTagName);
                $matchesClose = [];
                if (!empty($openTagName) && preg_match('~^\s*({/' . $openTagName . '})(.*)$~is', $this->string, $matchesClose)) {
                    $this->string = $matchesClose[2];
                    $node->setCloseTag($matchesClose[1]);
                }
            }
            
            if (!empty($node)) {
                if (!empty($parentNode)) {
                    $parentNode->append($node);
                }
            }

            // Если долистали до закрывающего родительского тега, выходим из этого уровня
            if (preg_match('~^\s*(</' . $parentOpenTagName . '>)(.*)$~is', $this->string) || preg_match('~^\s*({/' . $parentOpenTagName . '})(.*)$~is', $this->string)) {
                return null;
            }
        }
        return $parentNode;
    }
    
}