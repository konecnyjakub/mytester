<?php
declare(strict_types=1);

namespace MyTester;

use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

/**
 * Extension for automated tests runner that handles .errors files
 *
 * @author Jakub Konečný
 */
final readonly class ErrorsFilesExtension implements ITesterExtension
{
    public function __construct(private string $folder)
    {
    }

    public function getEventsPreRun(): array
    {
        return [
            [$this, "clearErrorsFiles"],
        ];
    }

    public function getEventsAfterRun(): array
    {
        return [];
    }

    public function getEventsBeforeTestCase(): array
    {
        return [];
    }

    public function getEventsAfterTestCase(): array
    {
        return [
            [$this, "saveErrors"],
        ];
    }

    /**
     * @internal
     */
    public function clearErrorsFiles(): void
    {
        $files = Finder::findFiles("*.errors")->in($this->folder);
        foreach ($files as $name => $file) {
            try {
                FileSystem::delete($name);
            } catch (IOException) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
            }
        }
    }

    /**
     * @internal
     */
    public function saveErrors(Events\TestCaseFinished $event): void
    {
        $jobs = $event->testCase->jobs;
        foreach ($jobs as $job) {
            if ($job->result === JobResult::FAILED && strlen($job->output) > 0) {
                file_put_contents("$this->folder/$job->name.errors", $job->output . "\n");
            }
        }
    }
}
