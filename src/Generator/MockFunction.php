<?php

namespace Kartavik\PHPMock\Generator;

use Kartavik\PHPMock;

/**
 * Generates the mock function.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockFunction
{
    /** @var string The internal name for optional parameters. */
    const DEFAULT_ARGUMENT = "optionalParameter";

    /** @var PHPMock\Mock The mock. */
    protected $mock;

    /** @var \Text_Template The function template. */
    protected $template;

    /**
     * Sets the mock.
     *
     * @param PHPMock\Mock $mock The mock.
     * @param \Text_Template $template
     */
    public function __construct(PHPMock\Mock $mock, \Text_Template $template = null)
    {
        $this->mock = $mock;
        $this->template = $template ?? new \Text_Template(__DIR__ . "/function.tpl");
    }

    /**
     * Defines the mock function.
     *
     * @SuppressWarnings(PHPMD)
     */
    public function defineFunction()
    {
        $name = $this->mock->getName();

        $parameterBuilder = new ParameterBuilder();
        $parameterBuilder->build($name);

        $data = [
            "namespace" => $this->mock->getNamespace(),
            "name" => $name,
            "fqfn" => $this->mock->getFQFN(),
            "signatureParameters" => $parameterBuilder->getSignatureParameters(),
            "bodyParameters" => $parameterBuilder->getBodyParameters(),
        ];
        $this->template->setVar($data, false);
        $definition = $this->template->render();

        eval($definition);
    }

    /**
     * Removes optional arguments.
     *
     * @param array $arguments The arguments.
     */
    public static function removeDefaultArguments(array &$arguments): void
    {
        foreach ($arguments as $key => $argument) {
            if ($argument === self::DEFAULT_ARGUMENT) {
                unset($arguments[$key]);
            }
        }
    }

    /**
     * Calls the enabled mock, or the built-in function otherwise.
     *
     * @param string $functionName The function name.
     * @param string $fqfn The fully qualified function name.
     * @param array $arguments The arguments.
     *
     * @return mixed The result of the called function.
     * @see Mock::define()
     * @SuppressWarnings(PHPMD)
     */
    public static function call(string $functionName, string $fqfn, array &$arguments)
    {
        $mock = PHPMock\MockRegistry::getMock($fqfn);

        self::removeDefaultArguments($arguments);

        if (empty($mock)) {
            // call the built-in function if the mock was not enabled.
            return call_user_func_array($functionName, $arguments);
        } else {
            // call the mock function.
            return $mock->call($arguments);
        }
    }
}
