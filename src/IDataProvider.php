<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Data provider for {@see TestCase}
 *
 * @author Jakub Konečný
 */
interface IDataProvider
{
    /**
     * Get data for a test method
     *
     * @return iterable<string|int, mixed>[] Values for consecutive calls of the method
     */
    public function getData(object $class, string $method): iterable;
}
