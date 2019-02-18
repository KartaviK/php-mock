<?php

namespace Kartavik\PHPMock\Tests\environment;

use Kartavik\PHPMock\Environment;
use PHPUnit\Framework\TestCase;

/**
 * Tests SleepBuilder.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class SleepEnvironmentBuilderTest extends TestCase
{
    /** @var Environment\Mock The build Environment. */
    private $environment;

    protected function setUp(): void
    {
        $builder = new Environment\SleepBuilder();
        $builder->addNamespace(__NAMESPACE__)
            ->setTimestamp(1234);

        $this->environment = $builder->build();
        $this->environment->enable();
    }

    protected function tearDown(): void
    {
        $this->environment->disable();
    }

    public function testAddNamespace(): void
    {
        $builder = new Environment\SleepBuilder();
        $builder->addNamespace(__NAMESPACE__)
            ->addNamespace("testAddNamespace")
            ->setTimestamp(1234);

        $this->environment->disable();
        $this->environment = $builder->build();
        $this->environment->enable();

        $time = time();
        \testAddNamespace\sleep(123);
        sleep(123);

        $this->assertEquals(2 * 123 + $time, time());
        $this->assertEquals(2 * 123 + $time, \testAddNamespace\time());
    }

    public function testSleep(): void
    {
        $time = time();
        $microtime = microtime(true);
        sleep(1);

        $this->assertEquals($time + 1, time());
        $this->assertEquals($microtime + 1, microtime(true));
        $this->assertEquals($time + 1, date("U"));
    }

    /**
     * @param int $microseconds Microseconds.
     *
     * @dataProvider provideTestUsleep
     */
    public function testUsleep($microseconds): void
    {
        $time = time();
        $microtime = microtime(true);
        usleep($microseconds);

        $delta = $microseconds / 1000000;
        $this->assertEquals((int)($time + $delta), time());
        $this->assertEquals((int)($time + $delta), date("U"));
        $this->assertEquals($microtime + $delta, microtime(true));
    }

    public function provideTestUsleep(): array
    {
        return [
            [1000],
            [999999],
            [1000000],
        ];
    }

    public function testDate(): void
    {
        $time = time();
        sleep(100);

        $this->assertEquals($time + 100, date("U"));
    }
}
