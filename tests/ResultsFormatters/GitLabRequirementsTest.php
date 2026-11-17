<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class GitLabRequirements
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter GitLabRequirements")]
#[Group("resultsFormatters")]
final class GitLabRequirementsTest extends TestCase
{
    public function testRender(): void
    {
        $outputFormatter = new GitLabRequirements();
        $listener = new AutoListenerProvider();
        $listener->addSubscriber($outputFormatter);
        $eventDispatcher = new EventDispatcher($listener);
        $testCase1 = new TestCaseOne();
        $testCase1->eventDispatcher = $eventDispatcher;
        $testCase1->run();
        $testCase2 = new TestCaseTwo();
        $testCase2->eventDispatcher = $eventDispatcher;
        $testCase2->run();
        $testCase3 = new TestCaseThree();
        $testCase3->eventDispatcher = $eventDispatcher;
        $testCase3->run();
        $result = $outputFormatter->render();
        $this->assertMatchesFile(__DIR__ . "/requirements_output.json", $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new GitLabRequirements();
        $this->assertSame("/var/project/requirements.json", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/requirements.json", $outputFormatter->getOutputFileName("/var/project/sub1"));
        $outputFormatter->setOutputFileName("requirementsV2.json");
        $this->assertSame("/var/project/requirementsV2.json", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame(
            "/var/project/sub1/requirementsV2.json",
            $outputFormatter->getOutputFileName("/var/project/sub1")
        );
    }
}
