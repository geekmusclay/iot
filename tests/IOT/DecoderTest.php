<?php 

namespace Geekmusclay\IOT\Tests;

use Geekmusclay\IOT\Decoder;
use PHPUnit\Framework\TestCase;

class DecoderTest extends TestCase
{
    public function testDecoder()
    {
        $frame = 'FF6F0A';
        $result = Decoder::decode($frame, [
            [
                'from'   => 0,
                'to'     => 2,
                'name'   => 'part1',
                'origin' => '0123456789ABCDEF',
                'target' => '01',
                'subdetails' => [
                    [
                        'from'   => 0,
                        'to'     => 2,
                        'name'   => 'test',
                        'target' => '0123456789'
                    ]
                ]
            ],
            [
                'from'   => 2,
                'to'     => 2,
                'name'   => 'part2',
                'origin' => '0123456789ABCDEF',
                'target' => '01',
                'subdetails' => [
                    [
                        'from'   => 0,
                        'to'     => 2,
                        'name'   => 'test2',
                        'target' => '0123456789'
                    ]
                ]
            ],
            [
                'from'   => 4,
                'to'     => 2,
                'name'   => 'part3',
                'origin' => '0123456789ABCDEF',
                'target' => '01',
                'subdetails' => []
            ]
        ]);

        $this->assertEquals($result['part1'], '11111111');
        $this->assertEquals($result['part2'], '01101111');
        $this->assertEquals($result['part3'], '00001010');

        $this->assertEquals($result['subdetails']['test'], '3');
        $this->assertEquals($result['subdetails']['test2'], '1');
    }
}