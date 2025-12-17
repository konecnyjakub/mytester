<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Events;
use MyTester\TesterExtension;

/**
 * Code coverage extension for automated tests runner
 *
 * @author Jakub Konečný
 */
final readonly class CodeCoverageExtension implements TesterExtension
{
    public function __construct(private Collector $collector)
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\RunnerStarted::class => [
                ["onRunnerStarted", ],
            ],
            Events\TestsFinished::class => [
                ["onTestsFinished", ],
            ],
        ];
    }

    /**
     * @throws CodeCoverageException
     */
    public function onRunnerStarted(Events\RunnerStarted $event): void
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
     * @throws CodeCoverageException
     */
    public function onTestsFinished(Events\TestsFinished $event): void
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
                    [CodeCoverageException::NO_ENGINE_AVAILABLE, CodeCoverageException::COLLECTOR_NOT_STARTED, ],
                    true
                )
            ) {
                return;
            }
            throw $e;
        }
    }

    public function getName(): string
    {
        return "code coverage";
    }
}
