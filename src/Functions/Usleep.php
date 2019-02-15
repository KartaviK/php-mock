<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Functions;

/**
 * Mock function for usleep().
 *
 * This function doesn't sleep. It returns immediatly. All registered
 * Increment objects (time() or microtime() mocks) get increased by the
 * passed seconds.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class Usleep extends AbstractSleep
{
    protected function convertToSeconds($amount)
    {
        return $amount / 1000000;
    }
}
