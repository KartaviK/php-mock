<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Functions;

use Kartavik\PHPMock\Contract;

/**
 * Mock function which returns always the same value.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class FixedValue implements Contract\Functions\ProviderInterface, Contract\Functions\IncrementInterface
{
    /** @var mixed The fixed value for the function. */
    private $value;

    public function __construct($value = null)
    {
        $this->setValue($value);
    }
    
    /**
     * Returns this object as a callable for the mock function.
     *
     * @return callable The callable for this object.
     */
    public function getClosure(): callable
    {
        return function () {
            return $this->value;
        };
    }

    /**
     * Set the value.
     *
     * @param mixed $value The value.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function increment($increment): void
    {
        $this->value += $increment;
    }
}
