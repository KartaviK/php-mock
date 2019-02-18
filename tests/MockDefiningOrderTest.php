<?php

namespace Kartavik\PHPMock\Tests;

use Kartavik\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ordering of the mock creation.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockDefiningOrderTest extends TestCase
{
    /** @var PHPMock\Mock The mock. */
    private $mock;

    protected function tearDown(): void
    {
        if (isset($this->mock)) {
            $this->mock->disable();
        }
    }

    /**
     * Returns the built-in call to escapeshellcmd().
     *
     * @param string $command Shell command.
     *
     * @return string The built-in call.
     */
    private static function escapeshellcmd($command): string
    {
        return escapeshellcmd($command);
    }

    /**
     * Tests the restriction of Bug #68541.
     *
     * The fallback policy seems to be static for called class methods. This
     * is documented in Bug #68541. The mock function has to be defined before
     * the first call in a class.
     *
     * When this bug fails, PHP behaviour changed its behaviour and the
     * documentation can be updated.
     *
     * @link https://bugs.php.net/bug.php?id=68541 Bug #68541
     */
    public function testDefineBeforeFirstCallRestriction(): void
    {
        /*
         * HHVM did fix this bug already.
         *
         * See https://github.com/sebastianbergmann/phpunit/issues/1356
         * for a better syntax.
         */
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped();
        }

        $function = __NAMESPACE__ . '\escapeshellcmd';
        $this->assertFalse(function_exists($function));

        self::escapeshellcmd("foo");

        $builder = new PHPMock\MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("escapeshellcmd")
            ->setFunctionProvider(new PHPMock\Functions\FixedValue("foo"));

        $this->mock = $builder->build();
        $this->mock->enable();

        $this->assertTrue(function_exists($function));
        $this->assertEquals("foo", escapeshellcmd("bar"));
        $this->assertEquals("bar", self::escapeshellcmd("bar"));
    }

    public function testDefiningAfterCallingUnqualified(): void
    {
        $function = __NAMESPACE__ . '\highlight_string';
        $this->assertFalse(function_exists($function));
        highlight_string("foo", true);

        $builder = new PHPMock\MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("highlight_string")
            ->setFunctionProvider(new PHPMock\Functions\FixedValue("bar"));

        $this->mock = $builder->build();
        $this->mock->enable();

        $this->assertTrue(function_exists($function));
        $this->assertEquals("bar", highlight_string("foo"));
    }

    public function testDefiningAfterCallingQualified(): void
    {
        $function = __NAMESPACE__ . '\str_word_count';
        $this->assertFalse(function_exists($function));
        \str_word_count("foo");

        $builder = new PHPMock\MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setName("str_word_count")
            ->setFunctionProvider(new PHPMock\Functions\FixedValue("bar"));

        $this->mock = $builder->build();
        $this->mock->enable();

        $this->assertTrue(function_exists($function));
        $this->assertEquals("bar", str_word_count("foo"));
    }
}
