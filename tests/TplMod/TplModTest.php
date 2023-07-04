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
        $resultHtml = ltrim($resultHtml, "\n");
        
        $this->assertEquals($expectedResult, $resultHtml);
    }
    
    public function applyModDataProvider(): array
    {
        return [
            [
                (new TplChangeDTO('<div class="foo">', ''))->setRemove(),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('<div class="foo">', ''))->setHtml('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <span>test</span>\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "        <span>test</span>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setAppendBefore('<head></head>'),
                "<html>\n" .
                "    <head></head>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?ass="bar"'))->setAppendBefore('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <span>test</span>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('<body>', ''))->setPrepend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <span>test</span>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setAppendAfter('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <span>test</span>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setHtml('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <span>test</span>\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setText('Hello world'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            Hello world\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setReplace('<div class="foo" data-text="success">'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\" data-text=\"success\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>"
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setComment('myModule')
                    ->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <!--myModule-->\n" .
                "            <span>test</span>\n" .
                "            <!--/myModule-->\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
                true, // debug comment
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setComment('myModule')
                    ->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <span>test</span>\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
                false, // debug comment
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setParent()->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "        <span>test</span>\n" .
                "    </body>\n" .
                "</html>",
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setParent()->setPrepend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <span>test</span>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
            ],
            [
                (new TplChangeDTO('class="foo"', ''))->setClosestFind('html')
                    ->setPrepend('<head></head>'),
                "<html>\n" .
                "    <head></head>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
            ],
            [
                (new TplChangeDTO('', '.*?s="foo"'))->setClosestLike('.*?ml>')
                    ->setPrepend('<head></head>'),
                "<html>\n" .
                "    <head></head>\n" .
                "    <body>\n" .
                "        <div class=\"foo\"></div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
            ],
            [
                (new TplChangeDTO('body', ''))->setChildrenFind('class="foo"')
                    ->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <span>test</span>\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
            ],
            [
                (new TplChangeDTO('body', ''))->setChildrenLike('.*?"foo"')
                    ->setAppend('<span>test</span>'),
                "<html>\n" .
                "    <body>\n" .
                "        <div class=\"foo\">\n" .
                "            <span>test</span>\n" .
                "        </div>\n" .
                "        <div class=\"bar\"></div>\n" .
                "    </body>\n" .
                "</html>",
            ],
        ];
    }
}