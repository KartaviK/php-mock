<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Collection;

use Kartavik\PHPMock\Contract;
use Wearesho\BaseCollection;

/**
 * Class Increment
 * @package Kartavik\PHPMock\Collection
 *
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class Increment extends BaseCollection
{
    final public function type(): string
    {
        return Contract\Functions\IncrementInterface::class;
    }
}
