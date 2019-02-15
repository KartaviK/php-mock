<?php

namespace Kartavik\PHPMock\Generator;

/**
 * Builder for the mocked function parameters.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 * @internal
 */
class ParameterBuilder
{
    /** @var string The signature's parameters. */
    protected $signatureParameters;

    /** @var string The body's parameter access list. */
    protected $bodyParameters;

    /**
     * Builds the parameters for an existing function.
     *
     * @param $functionName
     *
     * @throws \ReflectionException
     */
    public function build($functionName): void
    {
        if (!\function_exists($functionName)) {
            return;
        }

        $function = new \ReflectionFunction($functionName);
        $signatureParameters = [];
        $bodyParameters = [];

        foreach ($function->getParameters() as $reflectionParameter) {
            if ($this->isVariadic($reflectionParameter)) {
                break;
            }

            $parameter = $reflectionParameter->isPassedByReference()
                ? "&$$reflectionParameter->name"
                : "$$reflectionParameter->name";

            $signatureParameter = $reflectionParameter->isOptional()
                ? \sprintf("%s = '%s'", $parameter, MockFunction::DEFAULT_ARGUMENT)
                : $parameter;

            $signatureParameters[] = $signatureParameter;
            $bodyParameters[] = $parameter;
        }

        $this->signatureParameters = \implode(", ", $signatureParameters);
        $this->bodyParameters = \implode(", ", $bodyParameters);
    }

    /**
     * Returns whether a parameter is variadic.
     *
     * @param \ReflectionParameter $parameter The parameter.
     *
     * @return boolean True, if the parameter is variadic.
     */
    private function isVariadic(\ReflectionParameter $parameter): bool
    {
        if ($parameter->name == "...") {
            // This is a variadic C-implementation before PHP-5.6.
            return true;
        }

        if (\method_exists($parameter, "isVariadic")) {
            return $parameter->isVariadic();
        }

        return false;
    }

    /**
     * Returns the signature's parameters.
     *
     * @return string The signature's parameters.
     */
    public function getSignatureParameters(): ?string
    {
        return $this->signatureParameters;
    }

    /**
     * Returns the body's parameter access list.
     *
     * @return string The body's parameter list.
     */
    public function getBodyParameters(): ?string
    {
        return $this->bodyParameters;
    }
}
