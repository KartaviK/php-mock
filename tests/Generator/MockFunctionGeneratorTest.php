<?php

namespace Kartavik\PHPMock\Tests\Generator;

use Kartavik\PHPMock\Generator\MockFunction;
use PHPUnit\Framework\TestCase;

/**
 * Tests MockFunction.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 */
class MockFunctionGeneratorTest extends TestCase
{
    /**
     * Tests removeDefaultArguments().
     *
     * @param array $expected The expected result.
     * @param array $arguments The input arguments.
     *
     * @test
     * @dataProvider provideTestRemoveDefaultArguments
     */
    public function testRemoveDefaultArguments(array $expected, array $arguments)
    {
        MockFunction::removeDefaultArguments($arguments);
        $this->assertEquals($expected, $arguments);
    }

    public function provideTestRemoveDefaultArguments(): array
    {
        return [
            [[], []],
            [[1], [1]],
            [[1, 2], [1, 2]],
            [[null], [null]],
            [[], [MockFunction::DEFAULT_ARGUMENT]],
            [[], [MockFunction::DEFAULT_ARGUMENT, MockFunction::DEFAULT_ARGUMENT]],
            [[1], [1, MockFunction::DEFAULT_ARGUMENT]],
            [[null], [null, MockFunction::DEFAULT_ARGUMENT]],
            [[1], [1, MockFunction::DEFAULT_ARGUMENT, MockFunction::DEFAULT_ARGUMENT]],
        ];
    }
}
