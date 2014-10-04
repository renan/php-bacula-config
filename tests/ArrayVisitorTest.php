<?php
namespace Renan\Bacula\Config\Test;

use Renan\Bacula\Config\ArrayVisitor;

use PHPUnit_Framework_TestCase;
use Hoa\Compiler\Llk\Llk;
use Hoa\File;

class ArrayVisitorTest extends PHPUnit_Framework_TestCase
{
    public function testVisit()
    {
        $config = <<<CONFIG
Pool {
    Name = Default
    Action On Purge = Truncate
}

# My best fileset yet
Fileset {
    Name = "First Fileset"
    Include {
        Options {
            compression="GZIP" # Yay for compression
        }
        File = "/path/to/folder"
    }

    # This folder is too big, a separate fileset should be used
    Exclude {
        File = "/folder/too/big"
    }
}

Fileset {
    Name = "Second Fileset"
}
CONFIG;
        $expected = [
            [
                'type' => 'pool',
                'options' => [
                    [
                        'key' => 'Name',
                        'value' => 'Default'
                    ],
                    [
                        'key' => 'Action On Purge',
                        'value' => 'Truncate'
                    ]
                ]
            ],
            '# My best fileset yet',
            [
                'type' => 'fileset',
                'options' => [
                    [
                        'key' => 'Name',
                        'value' => 'First Fileset'
                    ],
                    [
                        'type' => 'include',
                        'options' => [
                            [
                                'type' => 'options',
                                'options' => [
                                    [
                                        'key' => 'compression',
                                        'value' => 'GZIP'
                                    ],
                                    '# Yay for compression'
                                ]
                            ],
                            [
                                'key' => 'File',
                                'value' => '/path/to/folder'
                            ]
                        ]
                    ],
                    '# This folder is too big, a separate fileset should be used',
                    [
                        'type' => 'exclude',
                        'options' => [
                            [
                                'key' => 'File',
                                'value' => '/folder/too/big'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'fileset',
                'options' => [
                    [
                        'key' => 'Name',
                        'value' => 'Second Fileset'
                    ]
                ]
            ]
        ];

        $compiler = Llk::load(new File\Read(dirname(__DIR__) . '/src/Resources.pp'));
        $visitor = new ArrayVisitor();
        $ast = $compiler->parse($config);
        $result = $visitor->visit($ast);

        $this->assertEquals($result, $expected);
    }
}
