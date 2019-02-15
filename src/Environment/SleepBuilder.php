<?php

declare(strict_types=1);

namespace Kartavik\PHPMock\Environment;

use Kartavik\PHPMock;

/**
 * Builds a sleep(), usleep(), date(), time() and microtime() mock Environment.
 *
 * In this Environment sleep() and usleep() don't sleep for real. Instead
 * they return immediatly and increase the amount of time in the mocks for
 * date(), time() and microtime().
 *
 * Example:
 * <code>
 * namespace foo;
 *
 * use Kartavik\PHPMockObject\Environment\SleepBuilder;
 *
 * $builder = new SleepBuilder();
 * $builder->addNamespace(__NAMESPACE__)
 *         ->setTimestamp(1417011228);
 *
 * $Environment = $builder->build();
 * $Environment->enable();
 *
 * // This won't delay the test for 10 seconds, but increase time().
 * sleep(10);
 * assert(1417011228 + 10 == time());
 *
 * // Now revert the effect so that sleep() and time() are not mocked anymore.
 * $Environment->disable();
 * </code>
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 */
class SleepBuilder
{
    /** @var array The namespaces for the mock Environment. */
    protected $namespaces;

    /** @var mixed the timestamp. */
    protected $timestamp;
    
    /**
     * Add a namespace for the mock Environment.
     *
     * @param string $namespace A namespace for the mock Environment.
     *
     * @return SleepBuilder
     */
    public function addNamespace($namespace): SleepBuilder
    {
        $this->namespaces[] = $namespace;

        return $this;
    }

    /**
     * Sets the mocked timestamp.
     *
     * If not set the mock will use the current time at creation time.
     * The timestamp can be an int, a float with microseconds or a string
     * in the microtime() format.
     *
     * @param mixed $timestamp The timestamp.
     *
     * @return SleepBuilder
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Builds a sleep(), usleep(), date(), time() and microtime() mock Environment.
     *
     * @return PHPMock\Environment\Mock
     */
    public function build(): PHPMock\Environment\Mock
    {
        $environment = $environment ?? new PHPMock\Environment\Mock(new PHPMock\Collection\MockObject());
        $builder = $builder ?? new PHPMock\MockBuilder();

        $incrementables = new PHPMock\Collection\Increment();
        foreach ($this->namespaces as $namespace) {
            $builder->setNamespace($namespace);

            // microtime() mock
            $microtime = $this->getFixedMicrotimeFunction();
            $builder->setName("microtime")
                ->setFunctionProvider($microtime);
            $environment->addMock($builder->build());

            // time() mock
            $builder->setName("time")
                ->setCallback([$microtime, "getTime"]);
            $environment->addMock($builder->build());

            // date() mock
            $date = new PHPMock\Functions\FixedDate($this->timestamp);
            $builder->setName("date")
                    ->setFunctionProvider($date);
            $environment->addMock($builder->build());

            $incrementables[] = $microtime;
            $incrementables[] = $date;
        }

        // Need a complete list of $incrementables.
        foreach ($this->namespaces as $namespace) {
            $builder->setNamespace($namespace);
            // sleep() mock
            $builder->setName("sleep")
                ->setFunctionProvider(new PHPMock\Functions\Sleep($incrementables));
            $environment->addMock($builder->build());

            // usleep() mock
            $builder->setName("usleep")
                ->setFunctionProvider(new PHPMock\Functions\Usleep($incrementables));
            $environment->addMock($builder->build());
        }

        return $environment;
    }

    protected function getFixedMicrotimeFunction(): PHPMock\Functions\FixedMicrotime
    {
        return new PHPMock\Functions\FixedMicrotime($this->timestamp);
    }
}
