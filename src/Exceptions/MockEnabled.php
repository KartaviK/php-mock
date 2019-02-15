<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Exceptions;

/**
 * Exception when enabling a mock for an already mocked function.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 * @see Mock::enable()
 */
class MockEnabled extends \Exception
{
    /** @var string */
    protected $name;

    public function __construct(string $name, int $code = 0, \Throwable $previous = null)
    {
        $this->name = $name;

        parent::__construct(
            "[{$name}] is already enabled. Call [disable()] on the existing mock.",
            $code,
            $previous
        );
    }

    public function getName(): string
    {
        return $this->name;
    }
}
