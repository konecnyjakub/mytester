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
    public function __construct(private TestsFolderProvider $folderProvider)
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestsStarted::class => [
                ["onTestsStarted", ],
            ],
            Events\TestSuiteFinished::class => [
                ["onTestSuiteFinished", ],
            ],
        ];
    }

    public function onTestsStarted(Events\TestsStarted $event): void
    {
        $files = Finder::findFiles("*.errors")->in($this->folderProvider->folder);
        foreach ($files as $name => $file) {
            try {
                FileSystem::delete($name);
            } catch (IOException) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
            }
        }
    }

    public function onTestSuiteFinished(Events\TestSuiteFinished $event): void
    {
        $jobs = $event->testSuite->jobs;
        foreach ($jobs as $job) {
            if ($job->result === JobResult::FAILED && strlen($job->output) > 0) {
                file_put_contents("{$this->folderProvider->folder}/$job->name.errors", $job->output . "\n");
            }
        }
    }

    public function getName(): string
    {
        return "error files";
    }
}
