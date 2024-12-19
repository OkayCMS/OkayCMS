<?php


namespace TplMod;


use Okay\Core\Config;
use Okay\Core\Modules\DTO\TplChangeDTO;
use Okay\Core\TplMod\Nodes\BaseNode;
use Okay\Core\TplMod\Nodes\HtmlNode;
use Okay\Core\TplMod\Parser;
use Okay\Core\TplMod\TplMod;

class TplModTest extends \PHPUnit\Framework\TestCase
{

    protected BaseNode $baseNode;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->baseNode = new BaseNode('document');
        $divFoo = new HtmlNode('<div class="foo">', '</div>');
        $divBar = new HtmlNode('<div class="bar">', '</div>');
        $body = new HtmlNode('<body>', '</body>');
        $html = new HtmlNode('<html>', '</html>');
        $body->append($divFoo);
        $body->append($divBar);
        $html->append($body);
        $this->baseNode->append($html);

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @param TplChangeDTO $changeDTO
     * @param string $expectedResult
     * @param bool $debug
     * @dataProvider applyModDataProvider
     * @throws \Exception
     */
    public function testApplyMod(TplChangeDTO $changeDTO, string $expectedResult, bool $debug = false)
    {
        $parserStub = $this->getMockBuilder(Parser::class)->getMock();
        $configStub = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $configStub->method('get')
            ->will($this->returnValueMap([
                [
                    'dev_mode',
                    $debug
                ],
            ]));
        $tplMod = new TplMod($parserStub, $configStub);

        $baseNode = clone $this->baseNode;

        $class = new \ReflectionClass(TplMod::class);

        $methodWalkByFile = $class->getMethod('walkByFile');
        $methodWalkByFile->setAccessible(true);
        $methodWalkByFile->invokeArgs($tplMod, [
            $baseNode,
            [$changeDTO]
        ]);

        $methodBuild = $class->getMethod('build');
        $methodBuild->setAccessible(true);
        $resultHtml = $methodBuild->invokeArgs($tplMod, [
            $baseNode
        ]);
        $resultHtml = ltrim($resultHtml, PHP_EOL);
        $this->assertEquals($expectedResult, $resultHtml);
    }
    
    public function applyModDataProvider(): array
    {
        return [
            [
                (new TplChangeDTO('<div class="foo">', ''))->setRemove(),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('<div class="foo">', ''))->setHtml('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setAppendBefore('<head></head>'),
                "<html>" .PHP_EOL.
                "    <head></head>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?ass="bar"'))->setAppendBefore('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setPrepend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setAppendAfter('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setHtml('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setText('Hello world'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            Hello world" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setReplace('<div class="foo" data-text="success">'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\" data-text=\"success\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setComment('myModule')
                    ->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <!--myModule-->" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "            <!--/myModule-->" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
                true, // debug comment
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setComment('myModule')
                    ->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
                false, // debug comment
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setParent()->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setParent()->setPrepend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <span>test</span>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setClosestFind('html')
                    ->setPrepend('<head></head>'),
                "<html>" .PHP_EOL.
                "    <head></head>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setClosestLike('.*?ml>')
                    ->setPrepend('<head></head>'),
                "<html>" .PHP_EOL.
                "    <head></head>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\"></div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
            [
                (new TplChangeDTO('body', ''))->setChildrenFind('class="foo"')
                    ->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
            [
                (new TplChangeDTO('body', ''))->setChildrenLike('.*?"foo"')
                    ->setAppend('<span>test</span>'),
                "<html>" .PHP_EOL.
                "    <body>" .PHP_EOL.
                "        <div class=\"foo\">" .PHP_EOL.
                "            <span>test</span>" .PHP_EOL.
                "        </div>" .PHP_EOL.
                "        <div class=\"bar\"></div>" .PHP_EOL.
                "    </body>" .PHP_EOL.
                "</html>",
            ],
        ];
    }
}