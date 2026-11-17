<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Annotations\Reader;
use MyTester\Events\TestFinished;
use MyTester\JobResult;
use MyTester\TestCase;

/**
 * GitLab Requirements results formatter for Tester
 * Outputs the project's met and unmet requirements into a file
 * @see https://docs.gitlab.com/user/project/requirements/
 *
 * @author Jakub Konečný
 */
final class GitLabRequirements extends AbstractResultsFormatter
{
    private readonly Reader $annotationsReader;

    /**
     * @var array<int, JobResult[]>
     */
    private array $requirementsResults = [];

    public function __construct(?Reader $annotationsReader = null)
    {
        $this->annotationsReader = $annotationsReader ?? Reader::create();
        $this->baseFileName = "requirements.json";
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            TestFinished::class => [
                ["reportTestFinished",],
            ],
        ];
    }

    public function reportTestFinished(TestFinished $event): void
    {
        $rm = $event->test->getCallbackReflection();
        if ($rm === null || !$rm->getClosureThis() instanceof TestCase) {
            return;
        }
        /** @var list<int>|null $annotation */
        $annotation = $this->annotationsReader->getAnnotation(
            "gitLabRequirements",
            $rm->getClosureThis()::class,
            $rm->getName()
        );
        if (is_array($annotation)) {
            foreach ($annotation as $requirementId) {
                if (!isset($this->requirementsResults[$requirementId])) {
                    $this->requirementsResults[$requirementId] = [];
                }
                $this->requirementsResults[$requirementId][] = $event->test->result;
            }
        }
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }

    public function render(): string
    {
        $result = [];
        foreach ($this->requirementsResults as $requirementId => $results) {
            if (in_array(JobResult::FAILED, $results, true)) {
                $result[(string) $requirementId] = "failed";
            } elseif (!array_all($results, static fn (JobResult $value) => $value === JobResult::SKIPPED)) {
                $result[(string) $requirementId] = "passed";
            }
        }
        return (string) json_encode($result, JSON_FORCE_OBJECT);
    }
}
