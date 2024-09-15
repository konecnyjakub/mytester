<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\ITesterExtension;

/**
 * Code coverage extension for automated tests runner
 *
 * @author Jakub Konečný
 * @internal
 */
final readonly class CodeCoverageExtension implements ITesterExtension
{
    public function __construct(private Collector $collector)
    {
    }

    public function getEventsPreRun(): array
    {
        return [
            [$this, "setupCodeCoverage"],
        ];
    }

    public function getEventsAfterRun(): array
    {
        return [
            [$this, "reportCodeCoverage"],
        ];
    }

    /**
     * @internal
     * @throws CodeCoverageException
     */
    public function setupCodeCoverage(): void
    {
        try {
            $this->collector->start();
        } catch (CodeCoverageException $e) {
            if ($e->getCode() !== CodeCoverageException::NO_ENGINE_AVAILABLE) {
                throw $e;
            }
        }
    }

    /**
     * @internal
     * @throws CodeCoverageException
     */
    public function reportCodeCoverage(): void
    {
        try {
            $engineName = $this->collector->getEngineName();
            echo "\nCollecting code coverage via $engineName\n";
            $this->collector->finish();
            $this->collector->write((string) getcwd());
        } catch (CodeCoverageException $e) {
            if (
                in_array(
                    $e->getCode(),
                    [CodeCoverageException::NO_ENGINE_AVAILABLE, CodeCoverageException::COLLECTOR_NOT_STARTED, ]
                )
            ) {
                return;
            }
            throw $e;
        }
    }
}
