<?php

namespace Kartavik\PHPMock\Contract\Functions;

/**
 * Interface IncrementInterface
 * @package Kartavik\PHPMock\Contract\Functions
 *
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
interface IncrementInterface
{
    /**
     * Increment a value.
     *
     * @param mixed $increment The amount of increase.
     * @internal
     */
    public function increment($increment): void;
}
