<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Exception thrown when an error occurred during code coverage data collection
 *
 * @author Jakub Konečný
 */
class CodeCoverageException extends \Exception
{
    public const int NO_ENGINE_AVAILABLE = 1;
    public const int COLLECTOR_NOT_STARTED = 2;
}
