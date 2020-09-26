Version 2.0.0-dev
- BC break: removed Runner
- simplified output, errors are now printed to tests_folder/job_name.errors
- BC break: removed parameter $successText of method Assert::tryAssertion()
- automated tests runner exits with 0/1 depending on whether all tests passed
- BC break: removed methods Environment::testStats(), Environment::getOutput(), Environment::checkFailed()
- added option to mark test method as supposed to fail
- removed option to save the output to file(s)
- BC break: removed second parameter of Environment::printLine()
- added script for automated tests runner, see README
- BC break: parameter $params in Job::__construct() must always be an array
- raised minimal version of PHP to 7.4
- marked some classes as final
- added event onExecute to Tester
- dropped support for Nette 2.4
- deprecated Assert
- BC break: added constructor for TestCase
- marked methods Environment::incCounter(), Environment::resetCounter(), Environment::addSkipped(), TestsRunner::autoload() as internal/private
- automated tests runner now supports *Test.php files, they should be used instead of *.phpt files

Version 1.1
- the environment for nette di extension is now set up in TestsRunner::execute()
- improved deprecation message for Runner
- added option to skip a test if current sapi is different
- added event onExecute to TestsRunner
- TestsRunner::execute() now returns whether the tests failed

Version 1.0
- added dependency on tracy/tracy
- BC break: Environment::testStats() takes 2 parameters now, second one is name is Tracy's timer
- added integration for nette/di
- Assert::tryAssertion() now supports custom messages for success and failure
- improved success and failure messages for some built-in assertions
- code refactoring
- added new assertion notCount
- all items of array are now evaluated in TestCase::checkSkip()
- added option to skip test if an extension is not loaded
- deprecated Runner
- added methods setUp and tearDown to TestCase which are called before/after each test method
- version of My Tester and PHP is shown at start now
- BC break: TestCase::getJobs() now return array of Job
- first parameter of Runner::addJob() can be instance of Job now
- added methods startUp and shutDown to TestCase which are called at start/end of the suit
- automated tests runner now sets up the environment himself
- showed total run time at the end of script

Version 0.9.4c
- fixed assertion same

Version 0.9.4b
- raised minimal version of PHP to 5.6
- corrected a typo

Version 0.9.4a
- raised minimal version of PHP to 5.5
- require version 2.4 of packages nette/reflection and nette/robot-loader

Version 0.9.4
- allowed conditional skipping of tests (in TestCase)
- small code refactoring
- print number of finished and skipped jobs for TestCase

Version 0.9.3
- test suits can have custom names
- possible BC break: made Environment::$taskCount protected (use Environment::getCounter() to get its value)
- possible BC break: made Environment::$output protected (use Environment::getOutput() to get its value)
- clarified error message when trying to set invalid output mode
- BC break: reworked parameters for test methods in TestCase, see README for more info

Version 0.9.2
- raised minimal version of PHP to 5.4
- added automated tests runner (requires package nette/robot-loader)
- added dependency on nette/reflection
- tests can have custom names
- tests (in TestCase) can be skipped

Version 0.9.1a
- small code refactoring
- added README with documentation
- made possible installation via composer

Version 0.9.1
- print correct line ending when run from browser
- small code refactoring

Version 0.9.0
- initial version
