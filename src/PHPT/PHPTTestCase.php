<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\Outcome;
use Konecnyjakub\PHPTRunner\Parser;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\Job;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;
use Nette\Utils\Finder;

/**
 * Test suite that runs .phpt files
 *
 * @author Jakub Konečný
 */
final class PHPTTestCase extends TestCase
{
    public function __construct(
        private readonly PhptRunner $runner,
        private readonly TestsFolderProvider $testsFolderProvider,
        private readonly TestSuitesSelectionCriteria $testSuitesSelectionCriteria
    ) {
        parent::__construct();
    }

    public function getSuiteName(object|string|null $class = null): string
    {
        return "PHPT files";
    }

    protected function getJobs(): array
    {
        /** @var Job[] $jobs */
        static $jobs = [];
        if (count($jobs) === 0) {
            $parser = new Parser();
            $files = Finder::findFiles("*.phpt")
                ->from($this->testsFolderProvider->folder)
                ->exclude($this->testSuitesSelectionCriteria->exceptFolders)
                ->sortByName()
                ->collect();
            foreach ($files as $file) {
                $parsedFile = $parser->parse($file->getPathname(), false);
                $jobs[] = new Job(
                    $parsedFile->testName !== "" ? $parsedFile->testName : $file->getPathname(),
                    function () use ($file) {
                        $this->runFile($file->getPathname());
                    }
                );
            }
            foreach ($jobs as $job) {
                $job->setEventDispatcher($this->getEventDispatcher());
            }
        }
        return $jobs;
    }

    public function runFile(string $filename): void
    {
        $result = $this->runner->runFile($filename);
        if ($result->outcome === Outcome::Skipped) {
            $this->markTestSkipped($result->output);
        } elseif ($result->outcome === Outcome::Failed) {
            $this->testResult(
                sprintf(
                    "Output is not %s but %s.",
                    $this->showValue($result->expectedOutput),
                    $this->showValue($result->output)
                ),
                false
            );
        } elseif ($result->outcome === Outcome::Passed) {
            $this->testResult("");
        }
    }
}
