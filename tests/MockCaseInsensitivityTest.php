<?php

namespace Kartavik\PHPMock\Tests;

use Kartavik\PHPMock\Exceptions\MockEnabled;
use Kartavik\PHPMock\Functions\FixedValue;
use Kartavik\PHPMock\Mock;
use Kartavik\PHPMock\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Tests Mock's case insensitivity.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 */
class MockCaseInsensitivityTest extends TestCase
{
    /** @var Mock */
    private $mock;
    
    protected function tearDown(): void
    {
        if (isset($this->mock)) {
            $this->mock->disable();
        }
    }

    /**
     * @dataProvider provideTestCaseSensitivity
     */
    public function testFailEnable($mockName)
    {
        $this->expectException(MockEnabled::class);
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName(strtolower($mockName))
                ->setFunctionProvider(new FixedValue(1234));

        $this->mock = $builder->build();
        $this->mock->enable();
        
        $failingMock = $builder->setName($mockName)->build();
        $failingMock->enable();
    }
    
    /**
     * Tests case insensitive mocks.
     *
     * @param string $mockName  The mock function name.
     *
     * @dataProvider provideTestCaseSensitivity
     */
    public function testCaseSensitivity($mockName)
    {
        $builder = new MockBuilder();
        $builder->setNamespace(__NAMESPACE__)
                ->setName($mockName)
                ->setFunctionProvider(new FixedValue(1234));

        $this->mock = $builder->build();
        $this->mock->enable();
        
        $this->assertEquals(1234, time(), "time() is not mocked");
        $this->assertEquals(1234, Time(), "Time() is not mocked");
        $this->assertEquals(1234, TIME(), "TIME() is not mocked");
    }
    
    /**
     * Returns test cases for testCaseSensitivity().
     *
     * @return string[][] Test cases.
     */
    public function provideTestCaseSensitivity()
    {
        return [
            ["TIME"],
            ["Time"],
            ["time"],
        ];
    }
}
