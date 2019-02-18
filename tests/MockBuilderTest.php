<?php

namespace Kartavik\PHPMock\Tests;

use Kartavik\PHPMock\Functions\FixedValue;
use Kartavik\PHPMock\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Tests MockBuilder.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("time")
            ->setCallback(
                function () {
                    return 1234;
                }
            );

        $mock = $builder->build();
        $mock->enable();
        $this->assertEquals(1234, time());
        $mock->disable();


        $builder->setFunctionProvider(new FixedValue(123));
        $mock = $builder->build();
        $mock->enable();
        $this->assertEquals(123, time());
        $mock->disable();
    }
}
