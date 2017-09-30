<?php
namespace MyTester;

/**
 * Tests for class Runner
 *
 * @author Jakub Konečný
 * @deprecated since version 1.0
 */
class TestJobsRunner {
  /**
   * Test for Environment::testResult()
   *
   * @return void
   * @deprecated since version 1.0
   */
  public function test() {
    Environment::testResult("Test", false);
  }
  
  /**
   * Test for Environment::testResult()
   *
   * @return void
   * @deprecated since version 1.0
   */
  public function testTwo() {
    Environment::testResult("Test");
  }
}
?>