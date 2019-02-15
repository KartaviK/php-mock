<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Functions;

use Kartavik\PHPMock;

/**
 * Abstract class for sleep() and usleep() functions.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
abstract class AbstractSleep implements PHPMock\Contract\Functions\ProviderInterface
{
    /** @var PHPMock\Collection\Increment Observing Increments. */
    protected $increments;
    
    /**
     * Sets the Increment objects.
     *
     * @param PHPMock\Collection\Increment $increments Observing Increments.
     *
     * @see addIncrements()
     */
    public function __construct(PHPMock\Collection\Increment $increments = null)
    {
        $this->increments = $increments ?? new PHPMock\Collection\Increment();
    }
    
    /**
     * Returns the sleep() mock function.
     *
     * A call will increase all registered Increment objects.
     *
     * @return callable The callable for this object.
     */
    public function getClosure(): callable
    {
        return function ($amount) {
            /** @var PHPMock\Contract\Functions\IncrementInterface $increment */
            foreach ($this->increments as $increment) {
                $increment->increment($this->convertToSeconds($amount));
            }
        };
    }

    /**
     * Adds an Increment object.
     *
     * These objects are observing this function and get notified by
     * increasing the amount of passed time. Increments are used
     * for time() and microtime() mocks.
     *
     * @param PHPMock\Contract\Functions\IncrementInterface $increment Observing Increment.
     */
    public function addIncrements(PHPMock\Contract\Functions\IncrementInterface $increment): void
    {
        $this->increments->append($increment);
    }

    /**
     * Converts the sleep() parameter into seconds.
     *
     * @param int $amount Amount of time units.
     * @return mixed Seconds.
     * @internal
     */
    abstract protected function convertToSeconds($amount);
}
