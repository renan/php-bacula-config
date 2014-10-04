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
    Include {
        Options { signature = SHA1; onfs=no; fstype=ext2 }
        File = "/"
    }
}
CONFIG;
        $expected = [
            [
                'type' => 'pool',
                'resource' => [
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
                'resource' => [
                    [
                        'key' => 'Name',
                        'value' => 'First Fileset'
                    ],
                    [
                        'type' => 'include',
                        'resource' => [
                            [
                                'type' => 'options',
                                'resource' => [
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
                        'resource' => [
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
                'resource' => [
                    [
                        'key' => 'Name',
                        'value' => 'Second Fileset'
                    ],
                    [
                        'type' => 'include',
                        'resource' => [
                            [
                                'type' => 'options',
                                'resource' => [
                                    [
                                        'key' => 'signature',
                                        'value' => 'SHA1'
                                    ],
                                    [
                                        'key' => 'onfs',
                                        'value' => 'no'
                                    ],
                                    [
                                        'key' => 'fstype',
                                        'value' => 'ext2'
                                    ]
                                ]
                            ],
                            [
                                'key' => 'File',
                                'value' => '/'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $compiler = Llk::load(new File\Read(dirname(__DIR__) . '/src/Resources.pp'));
        $visitor = new ArrayVisitor();
        $ast = $compiler->parse($config);
        $result = $visitor->visit($ast);

        $this->assertEquals($expected, $result);
    }
}
