<?php
namespace MyTester;

/**
 * Test suite for class Job
 *
 * @author Jakub Konečný
 */
class JobTest {
  /**
   * Test for Environment::testResult()
   * 
   * @return void
   */
  function test() {
    Environment::testResult("Test");
  }
  
  /**
   * Test params for job
   * 
   * @param array $params
   * @param string $text
   * @return void
   */
  function testParams($params, $text) {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}

$test = new JobTest();
$job = new Job("Test Job", array($test, "test"));
$params = array(
  array("abc"), "def"
);
$job2 = new Job("Test Job with Params", array($test, "testParams"), $params);
$job3 = new Job("Test Skipped Job", array($test, "test"), NULL, true);

echo $job->execute();
echo $job2->execute();
echo $job3->execute();
?>