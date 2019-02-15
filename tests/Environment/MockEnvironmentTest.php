<?php

namespace Kartavik\PHPMock\Tests\Environment;

use Kartavik\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests Mock.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class MockEnvironmentTest extends TestCase
{
    /** @var PHPMock\Environment\Mock The tested Environment. */
    protected $environment;

    protected function setUp(): void
    {
        $builder = new PHPMock\MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
            ->setFunctionProvider(new PHPMock\Functions\FixedValue(1234));

        $this->environment = new PHPMock\Environment\Mock(
            new PHPMock\Collection\MockObject([
                $builder->setName("time")->build(),
                $builder->setName("rand")->build(),
            ])
        );
    }

    protected function tearDown(): void
    {
        $this->environment->disable();
    }

    public function testEnable(): void
    {
        $this->environment->enable();

        $this->assertEquals(1234, time());
        $this->assertEquals(1234, rand());
    }

    public function testDefine(): void
    {
        $this->environment->addMock(
            new PHPMock\Mock(__NAMESPACE__, "testDefine", function () {
            })
        );

        $this->environment->define();

        $this->assertTrue(function_exists("Kartavik\\PHPMock\\Tests\\Environment\\time"));
        $this->assertTrue(function_exists("Kartavik\\PHPMock\\Tests\\Environment\\rand"));
        $this->assertTrue(function_exists("Kartavik\\PHPMock\\Tests\\Environment\\testDefine"));
    }

    public function testDisable(): void
    {
        $this->environment->enable();
        $this->environment->disable();

        $this->assertNotEquals(1234, time());

        // Note: There's a tiny chance that this assertion might fail.
        $this->assertNotEquals(1234, rand());
    }
}
