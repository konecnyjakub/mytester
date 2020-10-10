<?php

declare(strict_types=1);

namespace MyTester;

/**
 * @internal
 */
class CodeCoverageException extends \Exception
{
    public const NO_ENGINE_AVAILABLE = 1;
    public const COLLECTOR_NOT_STARTED = 2;
}
