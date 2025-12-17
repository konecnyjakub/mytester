<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Code coverage engine for {@see Collector}
 * Is responsible for collecting raw code coverage data
 *
 * @author Jakub Konečný
 */
interface CodeCoverageEngine
{
    public function getName(): string;

    /**
     * Returns whether the engine can be used
     * Engine can and probably will depend on a PHP extension
     */
    public function isAvailable(): bool;

    /**
     * Starts collection of code coverage data
     */
    public function start(): void;

    /**
     * Finishes collection of code coverage data and return raw data about code coverage
     *
     * @return array<string, array<int, int>>
     */
    public function collect(): array;
}
