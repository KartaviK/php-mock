<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Functions;

use Kartavik\PHPMock\Contract;

/**
 * Mock function for date() which returns always the same time.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class FixedDate implements Contract\Functions\ProviderInterface, Contract\Functions\IncrementInterface
{
    /** @var int */
    protected $timestamp;

    /**
     * Set the timestamp.
     *
     * @param int $timestamp The timestamp, if ommited the current time.
     */
    public function __construct(int $timestamp = null)
    {
        if (\is_null($timestamp)) {
            $timestamp = \time();
        }

        if (!\is_numeric($timestamp)) {
            throw new \InvalidArgumentException("Timestamp should be numeric");
        }

        $this->timestamp = $timestamp;
    }

    /**
     * Returns the mocked date() function.
     *
     * @return callable The callable for this object.
     */
    public function getClosure(): callable
    {
        return function ($format, $timestamp = null) {
            if (\is_null($timestamp)) {
                $timestamp = $this->timestamp;
            }

            return \date($format, (int)$timestamp);
        };
    }

    public function increment($increment): void
    {
        $this->timestamp += $increment;
    }
}
