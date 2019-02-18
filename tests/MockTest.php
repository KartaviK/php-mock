<?php

namespace Kartavik\PHPMock\Tests;

use Kartavik\PHPMock\Helper\AbstractMockTest;
use Kartavik\PHPMock\Mock;

/**
 * Tests Mock.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockTest extends AbstractMockTest
{

    protected function defineFunction($namespace, $functionName): void
    {
        $mock = new Mock($namespace, $functionName, function () {
        });
        $mock->define();
    }

    protected function mockFunction(string $namespace, string $functionName, callable $function): void
    {
        $mock = new Mock($namespace, $functionName, $function);
        $mock->enable();
    }

    protected function disableMocks(): void
    {
        Mock::disableAll();
    }

    public function testEnable(): void
    {
        $mock = new Mock(
            __NAMESPACE__,
            "rand",
            function () {
                return 1234;
            }
        );
        $this->assertNotEquals(1234, rand());
        $mock->enable();
        $this->assertEquals(1234, rand());
    }

    public function testReenable(): void
    {
        $mock = new Mock(
            __NAMESPACE__,
            "time",
            function () {
                return 1234;
            }
        );
        $mock->enable();
        $mock->disable();
        $mock->enable();
        $this->assertEquals(1234, time());
    }

    public function testDisableAll(): void
    {
        $mock2 = new Mock(__NAMESPACE__, "min", "max");
        $mock2->enable();

        Mock::disableAll();

        $this->assertNotEquals(1234, time());
        $this->assertEquals(1, min([1, 2]));
    }
}
