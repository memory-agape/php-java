<?php
namespace PHPJava\Tests;

use PHPJava\Core\JavaArchive;
use PHPJava\Kernel\Types\_Byte;
use PHPJava\Kernel\Types\_Char;
use PHPJava\Kernel\Types\_Double;
use PHPJava\Kernel\Types\_Float;
use PHPJava\Kernel\Types\_Int;
use PHPUnit\Framework\TestCase;

class CastTest extends Base
{
    protected $fixtures = [
        'CastTest',
    ];

    public function testIntToShort()
    {
        $result = $this->initiatedJavaClasses['CastTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'testIntToShort',
                new _Int(1234)
            );

        $this->assertInstanceOf(_Int::class, $result);
        $this->assertEquals(1234, $result->getValue());
    }

    public function testIntToDouble()
    {
        $result = $this->initiatedJavaClasses['CastTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'testIntToDouble',
                new _Int(1234)
            );

        // check type
        $this->assertInstanceOf(_Double::class, $result);
        $this->assertEquals(1234, $result->getValue());
    }

    public function testIntToFloat()
    {
        $result = $this->initiatedJavaClasses['CastTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'testIntToFloat',
                new _Int(1234)
            );

        // check type
        $this->assertInstanceOf(_Float::class, $result);
        $this->assertEquals(1234, $result->getValue());
    }

    public function testIntToByte()
    {
        // Byte processing is special.
        $result = $this->initiatedJavaClasses['CastTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'testIntToByte',
                new _Int(123)
            );

        // check type
        $this->assertInstanceOf(_Int::class, $result);
        $this->assertEquals(123, $result->getValue());
    }


    public function testIntToChar()
    {
        // Char processing is special.
        $result = $this->initiatedJavaClasses['CastTest']
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'testIntToByte',
                new _Int(123)
            );

        // check type
        $this->assertInstanceOf(_Int::class, $result);
        $this->assertEquals(123, $result->getValue());
    }
}