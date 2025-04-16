<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\Outcome;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\TestCase;

/**
 * Test suite that runs a .phpt file
 *
 * @author Jakub Konečný
 */
abstract class PHPTTestCase extends TestCase
{
    public function __construct(private readonly PhptRunner $runner, private readonly string $filename)
    {
        parent::__construct();
    }

    public function getSuiteName(object|string|null $class = null): string
    {
        return $this->filename;
    }

    public function testFile(): void
    {
        $result = $this->runner->runFile($this->filename);
        if ($result->outcome === Outcome::Skipped) {
            $this->markTestSkipped($result->output);
        } elseif ($result->outcome === Outcome::Failed) {
            $this->testResult(
                "Output is not " . $this->showValue($result->expectedOutput) . " but " . $this->showValue($result->output) . ".",
                false
            );
        } elseif ($result->outcome === Outcome::Passed) {
            $this->testResult("");
        }
    }
}
