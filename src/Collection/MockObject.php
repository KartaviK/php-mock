<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Collection;

use Kartavik\PHPMock;
use Wearesho\BaseCollection;

/**
 * Class Mock
 * @package Kartavik\PHPMock\Collection
 *
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockObject extends BaseCollection
{
    final public function type(): string
    {
        return PHPMock\Mock::class;
    }
}
