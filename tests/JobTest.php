<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Attributes\TestSuite;

/**
 * Test suite for class Job
 *
 * @author Jakub Konečný
 * @testSuit JobTest
 */
#[TestSuite("JobTest")]
final class JobTest extends TestCase {
  protected function getJobs(): array {
    $test = new TestJobs();
    $job = new Job("Test Job", [$test, "test"]);
    $params = [
      ["abc"], "def"
    ];
    $job2 = new Job("Test Job with Params", [$test, "testParams"], $params);
    $job3 = new Job("Test Skipped Job", [$test, "test"], [], true);
    return [$job, $job2, $job3];
  }
}
?>