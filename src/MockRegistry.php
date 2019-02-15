<?php

namespace Kartavik\PHPMock;

/**
 * Enabled mock registry.
 *
 * @author Markus Malkusch <markus@malkusch.de>
 * @author Roman Varkuta <roman.varkuta@gmail.com>
 * @see MockBuilder
 * @internal
 */
class MockRegistry
{
    /** @var Mock[] Enabled mocks. */
    private static $mocks = [];

    /**
     * Returns true if the mock is already registered.
     *
     * @param Mock $mock The mock.
     *
     * @return bool True if the mock is registered.
     */
    public static function isRegistered(Mock $mock): bool
    {
        return isset(static::$mocks[$mock->getFQFN()]);
    }
    
    /**
     * Returns the registered mock.
     *
     * @param string $fqfn The fully qualified function name.
     *
     * @return Mock The registered Mock.
     * @see Mock::getFQFN()
     */
    public static function getMock(string $fqfn): ?Mock
    {
        return static::$mocks[$fqfn] ?? null;
    }
    
    /**
     * Registers a mock.
     *
     * @param Mock $mock The mock.
     */
    public static function register(Mock $mock): void
    {
        static::$mocks[$mock->getFQFN()] = $mock;
    }
    
    /**
     * Unregisters all mocks.
     */
    public static function unregisterAll(): void
    {
        static::$mocks = [];
    }
    
    /**
     * Unregisters a mock.
     *
     * @param string $fqfn The mock FQFN that need to unregister
     */
    public static function unregister(string $fqfn): void
    {
        unset(static::$mocks[$fqfn]);
    }
}
