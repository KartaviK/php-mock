<?php

namespace foo;

use Kartavik\PHPMock\Mock;
use Kartavik\PHPMock\MockBuilder;
use Kartavik\PHPMock\MockRegistry;
use Kartavik\PHPMock\Functions\FixedValue;
use Kartavik\PHPMock\Environment\SleepBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Tests the example from the documentation.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class ExampleTest extends TestCase
{
    protected function tearDown(): void
    {
        MockRegistry::unregisterAll();
    }

    public function testExample1(): void
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
        assert(time() == 1234);
        $this->assertEquals(1234, time());
    }

    public function testExample2(): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("time")
            ->setFunctionProvider(new FixedValue(12345));

        $mock = $builder->build();
        $mock->enable();
        assert(time() == 12345);
        $this->assertEquals(12345, time());
    }

    public function testExample3(): void
    {
        $builder = new SleepBuilder();
        $builder->addNamespace(__NAMESPACE__)
            ->setTimestamp(12345);

        $environment = $builder->build();
        $environment->enable();

        sleep(10);

        assert(12345 + 10 == time());
        $this->assertEquals(12345 + 10, time());
    }

    public function testExample4(): void
    {
        $this->expectException(\Exception::class);
        $function = function () {
            throw new \Exception();
        };
        $mock = new Mock(__NAMESPACE__, "time", $function);
        $mock->enable();
        try {
            time();
        } finally {
            $mock->disable();
        }
    }

    public function testExample5(): void
    {
        $time = new Mock(
            __NAMESPACE__,
            "time",
            function () {
                return 3;
            }
        );
        $time->enable();
        $this->assertEquals(3, time());
    }
}
