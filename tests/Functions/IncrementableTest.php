<?php

namespace Kartavik\PHPMock\Tests\Functions;

use Kartavik\PHPMock\Contract\Functions\IncrementInterface;
use Kartavik\PHPMock\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Tests Increment and all its implementations.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class IncrementableTest extends TestCase
{
    /**
     * Tests increment().
     *
     * @param mixed $expected The expected value.
     * @param mixed $increment The amount of increase.
     * @param IncrementInterface $incrementable The tested Increment.
     * @param callable $getValue The lambda for getting the value.
     *
     * @dataProvider provideTestIncrement
     */
    public function testIncrement(
        $expected,
        $increment,
        IncrementInterface $incrementable,
        callable $getValue
    ): void {
        $incrementable->increment($increment);
        $this->assertEquals($expected, $getValue($incrementable));
    }

    public function provideTestIncrement(): array
    {
        $getFixedValue = function (Functions\FixedValue $function) {
            return call_user_func($function->getClosure());
        };
        $getMicrotime = function (Functions\FixedMicrotime $function) {
            return $function->getMicrotime(true);
        };
        $getDate = function (Functions\FixedDate $function) {
            return call_user_func($function->getClosure(), "U");
        };
        return [
            [1, 1, new Functions\FixedValue(0), $getFixedValue],
            [2, 1, new Functions\FixedValue(1), $getFixedValue],
            [-1, -1, new Functions\FixedValue(0), $getFixedValue],

            [1, 1, new Functions\FixedMicrotime(0), $getMicrotime],
            [-1, -1, new Functions\FixedMicrotime(0), $getMicrotime],
            [2, 1, new Functions\FixedMicrotime(1), $getMicrotime],

            [1, 1, new Functions\FixedDate(0), $getDate],
            [-1, -1, new Functions\FixedDate(0), $getDate],
            [2, 1, new Functions\FixedDate(1), $getDate],

            [
                1.00000001,
                0.00000001,
                new Functions\FixedMicrotime(1),
                $getMicrotime
            ],
            [
                1.00000009,
                0.00000009,
                new Functions\FixedMicrotime(1),
                $getMicrotime
            ],
        ];
    }
}
