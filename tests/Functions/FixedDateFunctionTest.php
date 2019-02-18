<?php

namespace Kartavik\PHPMock\Tests\Functions;

use Kartavik\PHPMock\Functions\FixedDate;
use PHPUnit\Framework\TestCase;

/**
 * Tests FixedDate.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class FixedDateFunctionTest extends TestCase
{
    public function testGetDate(): void
    {
        $function = new FixedDate(strtotime("2013-3-3"));
        $this->assertEquals("3. 3. 2013", call_user_func($function->getClosure(), "j. n. Y"));
        $this->assertEquals("24. 3. 2015", call_user_func($function->getClosure(), "j. n. Y", strtotime("2015-3-24")));
    }
}
