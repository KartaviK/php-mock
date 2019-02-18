<?php

namespace Kartavik\PHPMock\Tests\Functions;

use Kartavik\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests AbstractSleep and all its implementations.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class AbstractSleepFunctionTest extends TestCase
{
    public function testSleepIncrementationOfAllIncrementables()
    {
        $value1 = new PHPMock\Functions\FixedValue(1);
        $value2 = new PHPMock\Functions\FixedValue(2);
        $sleep = new PHPMock\Functions\Sleep(new PHPMock\Collection\Increment([$value1, $value2]));

        call_user_func($sleep->getClosure(), 1);

        $this->assertEquals(2, call_user_func($value1->getClosure()));
        $this->assertEquals(3, call_user_func($value2->getClosure()));
    }

    /**
     * @dataProvider provideTestSleepIncrementation
     */
    public function testSleepIncrementation(
        PHPMock\Functions\AbstractSleep $sleepFunction,
        $amount,
        $expected
    ): void {
        $value = new PHPMock\Functions\FixedValue(0);
        $sleepFunction->addIncrements($value);
        call_user_func($sleepFunction->getClosure(), $amount);
        $this->assertEquals($expected, call_user_func($value->getClosure()));
    }

    public function provideTestSleepIncrementation(): array
    {
        return [
            [new PHPMock\Functions\Sleep(), 1, 1],
            [new PHPMock\Functions\Sleep(), 0, 0],

            [new PHPMock\Functions\Usleep(), 0, 0],
            [new PHPMock\Functions\Usleep(), 1000, 0.001],
            [new PHPMock\Functions\Usleep(), 1000000, 1],
        ];
    }
}
