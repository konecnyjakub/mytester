<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageEngine
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
     */
    public function collect(): array;
}
