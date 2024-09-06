<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

class CodeCoverageException extends \Exception
{
    public const int NO_ENGINE_AVAILABLE = 1;
    public const int COLLECTOR_NOT_STARTED = 2;
}
