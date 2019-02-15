<?php

namespace Kartavik\PHPMock\Tests;

use Kartavik\PHPMock\Exceptions\MockEnabled;
use PHPUnit\Framework\TestCase;

/**
 * Common tests for mocks.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 */
abstract class AbstractMockTest extends TestCase
{
    abstract protected function disableMocks();

    /**
     * Builds an enabled function mock.
     *
     * @param string $namespace The namespace.
     * @param string $functionName The function name.
     * @param callable $function The function mock.
     */
    abstract protected function mockFunction($namespace, $functionName, callable $function);

    /**
     * Defines the function mock.
     *
     * @param string $namespace The namespace.
     * @param string $functionName The function name.
     */
    abstract protected function defineFunction($namespace, $functionName);

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->disableMocks();
    }

    /**
     * Tests mocking a function without parameters.
     *
     * @test
     */
    public function testMockFunctionWithoutParameters()
    {
        $this->mockFunction(__NAMESPACE__, "getmyuid", function () {
            return 1234;
        });
        $this->assertEquals(1234, getmyuid());
    }

    /**
     * Tests mocking a previously mocked function again.
     *
     * @test
     * @depends testMockFunctionWithoutParameters
     */
    public function testRedefine()
    {
        $this->mockFunction(__NAMESPACE__, "getmyuid", function () {
            return 5;
        });
        $this->assertEquals(5, getmyuid());
    }

    /**
     * Tests mocking a function without parameters.
     *
     * @test
     */
    public function testMockFunctionWithParameters()
    {
        $this->mockFunction(__NAMESPACE__, "rand", function ($min, $max) {
            return $max;
        });
        $this->assertEquals(1234, rand(1, 1234));
    }

    /**
     * Tests mocking of an undefined function.
     *
     * @test
     */
    public function testUndefinedFunction()
    {
        $this->assertFalse(function_exists("testUndefinedFunction"));
        $this->mockFunction(__NAMESPACE__, "testUndefinedFunction", function ($arg) {
            return $arg + 1;
        });
        $result = testUndefinedFunction(1);
        $this->assertEquals(2, $result);
    }

    public function testFailEnable()
    {
        $this->expectException(MockEnabled::class);
        $name = "testFailEnable";
        $this->mockFunction(__NAMESPACE__, $name, "sqrt");
        $this->mockFunction(__NAMESPACE__, $name, "sqrt");
    }

    public function testPassingByValue()
    {
        $this->mockFunction(__NAMESPACE__, "testPassingByValue", function ($a) {
            return $a + 1;
        });

        // Tests passing directly the value.
        $this->assertEquals(3, testPassingByValue(2));
    }

    public function testPassingByReference()
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

    public function testPreserveArgumentDefaultValue()
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
    public function testResetToDefaultArgumentOfOriginalFunction()
    {
        $functionName = $this->buildPrivateFunctionName("testPreserveArgumentDefaultValue");
        $result = $functionName();
        $this->assertEquals("default", $result);
    }

    public function testCVariadic()
    {
        $this->mockFunction(__NAMESPACE__, "min", "max");

        $this->assertEquals(2, min(2, 1));
        $this->assertEquals(2, min([2, 1]));
    }

    /**
     * @depends testCVariadic
     */
    public function testCVariadicReset()
    {
        $this->assertEquals(1, min(2, 1));
        $this->assertEquals(1, min([2, 1]));
    }

    public function testDisableSetup()
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
    public function testDisable()
    {
        $this->assertNotEquals(1234, rand());
        $this->assertNotEquals(1234, mt_rand());
    }

    public function testImplicitDefine()
    {
        $functionName = $this->buildPrivateFunctionName("testDefine");
        $fqfn = __NAMESPACE__ . "\\$functionName";
        $this->assertFalse(function_exists($fqfn));
        $this->mockFunction(__NAMESPACE__, $functionName, "sqrt");
        $this->assertTrue(function_exists($fqfn));
    }

    public function testExplicitDefine()
    {
        $this->defineFunction(__NAMESPACE__, "escapeshellcmd");
        $this->escapeshellcmd("foo");

        $this->mockFunction(__NAMESPACE__, "escapeshellcmd", function () {
            return "bar";
        });

        $this->assertEquals("bar", self::escapeshellcmd("foo"));
    }

    private function escapeshellcmd($command)
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
    public function testBackupStaticAttributes()
    {
        $this->mockFunction(__NAMESPACE__, "testBackupStaticAttributes", "sqrt");
        $this->assertEquals(2, testBackupStaticAttributes(4));
    }

    public function provideTestBackupStaticAttributes()
    {
        return [
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            [],
            []
        ];
    }

    /**
     * @runInSeparateProcess
     */
    public function testRunInSeparateProcess()
    {
        $this->mockFunction(__NAMESPACE__, "time", function () {
            return 123;
        });
        $this->assertEquals(123, time());
    }
}
