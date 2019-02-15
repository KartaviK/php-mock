<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Environment;

use Kartavik\PHPMock;

/**
 * Container for several mocks.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class Mock implements PHPMock\Deactivatable
{
    /**@var PHPMock\Mock[] Mock Environment */
    private $mocks = [];

    public function __construct(PHPMock\Collection\MockObject $mocks)
    {
        $this->mocks = $mocks;
    }

    public function addMock(PHPMock\Mock $mock): Mock
    {
        $this->mocks[] = $mock;

        return $this;
    }

    public function enable(): void
    {
        foreach ($this->mocks as $mock) {
            $mock->enable();
        }
    }

    public function define(): void
    {
        foreach ($this->mocks as $mock) {
            $mock->define();
        }
    }

    public function disable(): void
    {
        foreach ($this->mocks as $mock) {
            $mock->disable();
        }
    }
}
