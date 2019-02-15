<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Functions;

/**
 * Mock function for sleep().
 *
 * This function doesn't sleep. It returns immediatly. All registered
 * Increment objects (time() or microtime() mocks) get increased by the
 * passed seconds.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class Sleep extends AbstractSleep
{
    protected function convertToSeconds($amount)
    {
        return $amount;
    }
}
