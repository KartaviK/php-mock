<?php

namespace Kartavik\PHPMock\Helper;

use Kartavik\PHPMock\Exceptions\MockEnabled;
use PHPUnit\Framework\TestCase;

/**
 * Common tests for mocks.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
abstract class AbstractMockTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->disableMocks();
    }

    abstract protected function disableMocks(): void;

    /**
     * Builds an enabled function mock.
     *
     * @param string $namespace The namespace.
     * @param string $functionName The function name.
     * @param callable $function The function mock.
     */
    abstract protected function mockFunction(string $namespace, string $functionName, callable $function): void;

    /**
     * Defines the function mock.
     *
     * @param string $namespace The namespace.
     * @param string $functionName The function name.
     */
    abstract protected function defineFunction(string $namespace, string $functionName);

    /**
     * Tests mocking a function without parameters.
     */
    public function testMockFunctionWithoutParameters(): void
    {
        $this->mockFunction(__NAMESPACE__, "getmyuid", function () {
            return 1234;
        });
        $this->assertEquals(1234, getmyuid());
    }

    /**
     * Tests mocking a previously mocked function again.
     *
     * @depends testMockFunctionWithoutParameters
     */
    public function testRedefine(): void
    {
        $this->mockFunction(__NAMESPACE__, "getmyuid", function () {
            return 5;
        });
        $this->assertEquals(5, getmyuid());
    }

    /**
     * Tests mocking a function without parameters.
     */
    public function testMockFunctionWithParameters(): void
    {
        $this->mockFunction(__NAMESPACE__, "rand", function ($min, $max) {
            return $max;
        });
        $this->assertEquals(1234, rand(1, 1234));
    }

    /**
     * Tests mocking of an undefined function.
     */
    public function testUndefinedFunction(): void
    {
        $this->assertFalse(function_exists("testUndefinedFunction"));
        $this->mockFunction(__NAMESPACE__, "testUndefinedFunction", function ($arg) {
            return $arg + 1;
        });
        $result = testUndefinedFunction(1);
        $this->assertEquals(2, $result);
    }

    public function testFailEnable(): void
    {
        $this->expectException(MockEnabled::class);
        $name = "testFailEnable";
        $this->mockFunction(__NAMESPACE__, $name, "sqrt");
        $this->mockFunction(__NAMESPACE__, $name, "sqrt");
    }

    public function testPassingByValue(): void
    {
        $this->mockFunction(__NAMESPACE__, "testPassingByValue", function ($a) {
            return $a + 1;
        });

        // Tests passing directly the value.
        $this->assertEquals(3, testPassingByValue(2));
    }

    public function testPassingByReference(): void
    {
        $this->mockFunction(__NAMESPACE__, "exec", function ($a, &$b, &$c) {
            $a = "notExpected";
            $b[] = "test1";
            $b[] = "test2";
            $c = "test";
        });

        $noReference = "expected";
        $b = [];
        $c = "";

        exec($noReference, $b, $c);
        $this->assertEquals(["test1", "test2"], $b);
        $this->assertEquals("test", $c);
        $this->assertEquals("test", $c);
        $this->assertEquals("expected", $noReference);
    }

    public function testPreserveArgumentDefaultValue(): void
    {
        $functionName = $this->buildPrivateFunctionName("testPreserveArgumentDefaultValue");

        eval("
            function $functionName(\$b = \"default\") {
                return \$b;
            }
        ");

        $this->mockFunction(
            __NAMESPACE__,
            $functionName,
            function ($arg = "expected") {
                return $arg;
            }
        );

        $fqfn = __NAMESPACE__ . "\\$functionName";
        $result = $fqfn();
        $this->assertEquals("expected", $result);
    }

    /**
     * @depends testPreserveArgumentDefaultValue
     */
    public function testResetToDefaultArgumentOfOriginalFunction(): void
    {
        $functionName = $this->buildPrivateFunctionName("testPreserveArgumentDefaultValue");
        $result = $functionName();
        $this->assertEquals("default", $result);
    }

    public function testCVariadic(): void
    {
        $this->mockFunction(__NAMESPACE__, "min", "max");

        $this->assertEquals(2, min(2, 1));
        $this->assertEquals(2, min([2, 1]));
    }

    /**
     * @depends testCVariadic
     */
    public function testCVariadicReset(): void
    {
        $this->assertEquals(1, min(2, 1));
        $this->assertEquals(1, min([2, 1]));
    }

    public function testDisableSetup(): void
    {
        $this->mockFunction(__NAMESPACE__, "rand", function () {
            return 1234;
        });
        $this->mockFunction(__NAMESPACE__, "mt_rand", function () {
            return 1234;
        });
        $this->assertEquals(1234, rand());
        $this->assertEquals(1234, mt_rand());
    }

    /**
     * @depends testDisableSetup
     */
    public function testDisable(): void
    {
        $this->assertNotEquals(1234, rand());
        $this->assertNotEquals(1234, mt_rand());
    }

    public function testImplicitDefine(): void
    {
        $functionName = $this->buildPrivateFunctionName("testDefine");
        $fqfn = __NAMESPACE__ . "\\$functionName";
        $this->assertFalse(function_exists($fqfn));
        $this->mockFunction(__NAMESPACE__, $functionName, "sqrt");
        $this->assertTrue(function_exists($fqfn));
    }

    public function testExplicitDefine(): void
    {
        $this->defineFunction(__NAMESPACE__, "escapeshellcmd");
        $this->escapeshellcmd("foo");

        $this->mockFunction(__NAMESPACE__, "escapeshellcmd", function () {
            return "bar";
        });

        $this->assertEquals("bar", self::escapeshellcmd("foo"));
    }

    private function escapeshellcmd($command): string
    {
        return escapeshellcmd($command);
    }

    private function buildPrivateFunctionName($name)
    {
        return $name . str_replace("\\", "_", get_class($this));
    }

    /**
     * @backupStaticAttributes enabled
     * @dataProvider provideTestBackupStaticAttributes
     */
    public function testBackupStaticAttributes(): void
    {
        $this->mockFunction(__NAMESPACE__, "testBackupStaticAttributes", "sqrt");
        $this->assertEquals(2, testBackupStaticAttributes(4));
    }

    public function provideTestBackupStaticAttributes(): \Traversable
    {
        foreach (\range(0, 11) as $i) {
            yield [];
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testRunInSeparateProcess(): void
    {
        $this->mockFunction(__NAMESPACE__, "time", function () {
            return 123;
        });
        $this->assertEquals(123, time());
    }
}
