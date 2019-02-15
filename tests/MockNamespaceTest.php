<?php

// A different namespace
namespace Kartavik\PHPMock\Tests\test;

use Kartavik\PHPMock\Mock;
use Kartavik\PHPMock\MockBuilder;
use Kartavik\PHPMock\Functions\FixedValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests Mock in a different namespace.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 */
class MockNamespaceTest extends TestCase
{
    /** @var Mock */
    private $mock;
    
    /** @var MockBuilder */
    private $builder;
    
    protected function setUp(): void
    {
        $this->builder = new MockBuilder();
        $this->builder
                ->setName("time")
                ->setFunctionProvider(new FixedValue(1234));
    }
    
    protected function tearDown(): void
    {
        if (! empty($this->mock)) {
            $this->mock->disable();
            unset($this->mock);
        }
    }

    /**
     * @dataprovider provideTestNamespace
     * @runInSeparateProcess
     */
    public function testDefiningNamespaces(): void
    {
        $this->builder->setNamespace(__NAMESPACE__);
        $this->mock = $this->builder->build();
        $this->mock->enable();
        
        $this->assertEquals(1234, time());
    }

    /**
     * @dataprovider provideTestNamespace
     */
    public function testRedefiningNamespaces(): void
    {
        $this->builder->setNamespace(__NAMESPACE__);
        $this->mock = $this->builder->build();
        $this->mock->enable();
        
        $this->assertEquals(1234, time());
    }
    
    /**
     * Provides namespaces for testNamespace().
     *
     * @return string[][] Namespaces.
     */
    public function provideTestNamespace()
    {
        return [
            [__NAMESPACE__],
            ['Kartavik\PHPMock\Tests\test'],
            ['\Kartavik\PHPMock\Tests\test'],
            ['Kartavik\PHPMock\Tests\test\\'],
            ['\Kartavik\PHPMock\Tests\test\\']
        ];
    }
}
