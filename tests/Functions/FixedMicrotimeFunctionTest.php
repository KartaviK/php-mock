<?php

namespace Kartavik\PHPMock\Tests\Functions;

use Kartavik\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests FixedMicrotime.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class FixedMicrotimeFunctionTest extends TestCase
{
    public function testSetMicrotime(): void
    {
        $function = new PHPMock\Functions\FixedMicrotime();
        $function->setMicrotime("0.00000001 1");
        $this->assertEquals("0.00000001 1", $function->getMicrotime());
    }

    public function testSetMicrotimeAsFloat(): void
    {
        $function = new PHPMock\Functions\FixedMicrotime();
        $function->setMicrotimeAsFloat(1.00000001);
        $this->assertEquals(1.00000001, $function->getMicrotime(true));
    }

    public function testGetMicrotime(): void
    {
        $function = new PHPMock\Functions\FixedMicrotime();
        $function->setMicrotimeAsFloat(1.00000001);
        $this->assertEquals(1.00000001, $function->getMicrotime(true));
        $this->assertEquals("0.00000001 1", $function->getMicrotime());
    }

    public function testGetCallable(): void
    {
        $function = new PHPMock\Functions\FixedMicrotime();
        $function->setMicrotimeAsFloat(1.00000001);

        $builder = new PHPMock\MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("microtime")
            ->setFunctionProvider($function);

        $mock = $builder->build();
        $mock->enable();
        $this->assertEquals("0.00000001 1", microtime());
        $this->assertEquals(1.00000001, microtime(true));

        $mock->disable();
    }

    public function testConstructCurrentTime(): void
    {
        $function = new PHPMock\Functions\FixedMicrotime();

        $this->assertGreaterThan($function->getMicrotime(true), \microtime(true));
        $this->assertGreaterThan(0, $function->getMicrotime(true));
    }

    /**
     * @dataProvider provideTestConstructFailsForInvalidArgument
     */
    public function testConstructFailsForInvalidArgument($timestamp): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PHPMock\Functions\FixedMicrotime($timestamp);
    }

    public function provideTestConstructFailsForInvalidArgument(): array
    {
        return [
            [true],
            [new \stdClass()]
        ];
    }

    /**
     * @dataProvider provideTestConstruct
     */
    public function testConstruct($timestamp, $expected): void
    {
        $function = new PHPMock\Functions\FixedMicrotime($timestamp);

        $this->assertEquals($expected, $function->getMicrotime(true));
    }

    public function provideTestConstruct(): array
    {
        return [
            ["0.00000001 1", 1.00000001],
            [1.00000001, 1.00000001],
        ];
    }
}
