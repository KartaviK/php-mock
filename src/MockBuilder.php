<?php

namespace Kartavik\PHPMock;

use Kartavik\PHPMock\Contract\Functions\ProviderInterface;

/**
 * Fluent API mock builder.
 *
 * Example:
 * <code>
 * namespace foo;
 *
 * use Kartavik\PHPMockObject\MockBuilder;
 * use Kartavik\PHPMockObject\Functions\FixedValue;
 *
 * $builder = new MockBuilder();
 * $builder->setNamespace(__NAMESPACE__)
 *         ->setName("time")
 *         ->setFunctionProvider(new FixedValue(1417011228));
 *
 * $mock = $builder->build();
 *
 * // The mock is not enabled yet.
 * assert (time() != 1417011228);
 *
 * $mock->enable();
 * assert (time() == 1417011228);
 *
 * // The mock is disabled and PHP's built-in time() is called.
 * $mock->disable();
 * assert (time() != 1417011228);
 * </code>
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockBuilder
{
    /** @var string namespace for the mock function. */
    protected $namespace;

    /** @var string function name of the mocked function. */
    protected $name;

    /** @var \Closure The function mock. */
    protected $callback;

    public function setNamespace($namespace): MockBuilder
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function setName($name): MockBuilder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the mock function.
     *
     * Use this method if you want to set the mocked behaviour with
     * a callable. Alternatively, you can use {@link setFunctionProvider()}
     * to set it with a {@link FunctionProvider}.
     *
     * @param callable $callback The mock function.
     *
     * @return MockBuilder
     * @see setFunctionProvider()
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Sets the mock function.
     *
     * Use this method if you want to set the mocked behaviour with
     * a {@link FunctionProvider}. Alternatively, you can use
     * {@link setFunction()} to set it with a callable.
     *
     * @param ProviderInterface $provider The mock function provider.
     *
     * @return MockBuilder
     * @see setCallback()
     */
    public function setFunctionProvider(ProviderInterface $provider)
    {
        return $this->setCallback($provider->getClosure());
    }

    /**
     * Builds a mock.
     *
     * @return Mock The mock.
     */
    public function build()
    {
        return new Mock($this->namespace, $this->name, $this->callback);
    }
}
