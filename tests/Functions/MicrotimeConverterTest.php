<?php

namespace Kartavik\PHPMock\Tests\Functions;

use Kartavik\PHPMock\Functions\MicrotimeConverter;
use PHPUnit\Framework\TestCase;

/**
 * Tests MicrotimeConverter.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MicrotimeConverterTest extends TestCase
{
    /**
     * Test convertStringToFloat().
     *
     * @param float $float The timestamp.
     * @param string $string The timestamp.
     *
     * @dataProvider provideFloatAndStrings
     */
    public function testConvertStringToFloat($float, $string): void
    {
        $converter = new MicrotimeConverter();
        $this->assertEquals($float, $converter->convertStringToFloat($string));
    }

    /**
     * Test convertFloatToString().
     *
     * @param float $float The timestamp.
     * @param string $string The timestamp.
     *
     * @dataProvider provideFloatAndStrings
     */
    public function testConvertFloatToString($float, $string): void
    {
        $converter = new MicrotimeConverter();
        $this->assertEquals($string, $converter->convertFloatToString($float));
    }

    public function provideFloatAndStrings(): array
    {
        return [
            [1.0, "0.00000000 1"],
            [1.00000001, "0.00000001 1"],
            [1.00000009, "0.00000009 1"],
            [1.1, "0.10000000 1"],
            [1.11, "0.11000000 1"],
            [1.9, "0.90000000 1"],
            [1.99999999, "0.99999999 1"],
        ];
    }
}
