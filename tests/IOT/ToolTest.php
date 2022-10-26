<?php 

namespace Geekmusclay\IOT\Tests;

use Geekmusclay\IOT\Tool;
use PHPUnit\Framework\TestCase;

class ToolTest extends TestCase
{
    public function testBaseConversion()
    {
        $binary = Tool::convBase('34', '0123456789', '01');
        $this->assertEquals('100010', $binary);

        $rbinary = Tool::convBase($binary, '01', '0123456789');
        $this->assertEquals('34', $rbinary);

        $hexa = Tool::convBase('34', '0123456789', '0123456789ABCDEF');
        $this->assertEquals('22', $hexa);

        $rhexa = Tool::convBase($hexa, '0123456789ABCDEF', '0123456789');
        $this->assertEquals('34', $rhexa);
    }
}