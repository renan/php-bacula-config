<?php
namespace Renan\Bacula\Config\Test;

use Renan\Bacula\Config\ArrayVisitor;

use PHPUnit_Framework_TestCase;
use Hoa\Compiler\Llk\Llk;
use Hoa\File;

class ArrayVisitorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->compiler = Llk::load(new File\Read(dirname(__DIR__) . '/src/Resources.pp'));
        $this->visitor = new ArrayVisitor();
    }

    /**
     * @dataProvider visitProvider
     */
    public function testVisit($config, array $expected)
    {
        $ast = $this->compiler->parse($config);
        $result = $this->visitor->visit($ast);
        $this->assertEquals($result, $expected);
    }

    public function visitProvider()
    {
        $tests = [];

        $config = <<<CONFIG
Include {
    File = "/path/to/folder"
    File = "/another/folder"
}
CONFIG;
        $expected = [
            [
                'Include', [
                    'File' => ['/path/to/folder', '/another/folder'],
                ],
            ],
        ];
        $tests['multiple pairs'] = compact('config', 'expected');

        $config = <<<CONFIG
Include {
    File = "/path/to/folder"; File = "/another/folder"
}
CONFIG;
        $expected = [
            [
                'Include', [
                    'File' => ['/path/to/folder', '/another/folder'],
                ],
            ],
        ];
        $tests['multiple pairs, one line'] = compact('config', 'expected');

        $config = <<<CONFIG
Include {
    File = "/path/to/folder"
}
Include {
    File = "/another/folder"
}
CONFIG;
        $expected = [
            [
                'Include', [
                    'File' => ['/path/to/folder'],
                ],
            ],
            [
                'Include', [
                    'File' => ['/another/folder'],
                ],
            ],
        ];
        $tests['repeated blocks'] = compact('config', 'expected');

        return $tests;
    }
}
