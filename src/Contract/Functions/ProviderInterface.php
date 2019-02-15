<?php

namespace Kartavik\PHPMock\Contract\Functions;

/**
 * Interface ProviderInterface
 * @package Kartavik\PHPMock\Contract\Functions
 *
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Returns this object as a callable for the mock function.
     *
     * @return callable The callable for this object.
     */
    public function getClosure(): callable;
}
