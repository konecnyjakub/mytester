Version 8.0.0-dev
- dropped support for Composer 2.0 and 2.1
- BC break: removed support for old way of providing 1 parameter to a test method via data provided
- BC break: removed support for using conditions with attribute Skip
- added events ExtensionsLoaded, RunnerStarted and RunnerFinished
- BC break: added methods getName and getSubscribedEvents to interface ITesterExtension, removed methods on* (it is now an event subscriber)
- InfoExtension (when added to automated tests runner) prints active extensions
- BC break: added method getSubscribedEvents to interface IResultsFormatter, removed methods report* (it is now an event subscriber)
- BC break: data providers can now also return iterable objects not just arrays

Version 7.3.1
- allowed installation konecnyjakub/event-dispatcher 2

Version 7.3.0
- errors/exception thrown in a test method are reported as failure instead of crashing the script
- added assertions assertArrayHasKey, assertArrayNotHasKey, assertSameSize and assertTriggersNoDeprecation
- added option to not have deprecations reported as warnings in a test method/test case
- deprecated using attribute Skip with conditions, use new attributes RequiresPhpVersion, RequiresPhpExtension, RequiresSapi or RequiresOsFamily instead
- improved error message for assertion assertType

Version 7.2.1
- passing invalid argument to assertion methods is now reported as an assertion failure instead of crashing the script

Version 7.2.0
- deprecations triggered in a test method are now reported as warnings
- it is now possible to also set background color with ConsoleColors::color()
- added assertion assertTriggersDeprecation

Version 7.1.0
- filename of generated code coverage report is now shown in console (if a report is generated and is saved into a file)
- callbacks in Job::$onAfterExecute receive Job as first parameter
- improved error message for assertion assertCount
- allowed passing multiple parameters to test methods via data provider
- show faulty data set (or custom name if set) if a test method with data provider fails

Version 7.0.0
- BC break: used interfaces for data provider and skip checker in TestCase
- BC break: used typed class constants
- BC break: made some methods in TAssertions final
- BC break: it is no longer possible to pass null to parameters $resultsFormatter and $testSuitesFinder in Tester's constructor
- BC break: made Tester::$testSuitesFinder readonly
- BC break: added method IResultsFormatter::setOutputFileName
- added TestCaseStarted, TestCaseFinished, DataProvider, SkipChecker, TestSuiteFactory, ComposerTestSuitesFinder, ChainTestSuitesFinder ContainerSuiteFactory, TestSuitesFinder, AssertionFailedException, InterruptedTestException, IncompleteTestException, SkippedTestException and IConsoleAwareResultsFormatter to public api
- BC break: removed method IResultsFormatter::setup
- BC break: Tester's constructor now takes TestsFolderProvider as its first parameter
- BC break: removed Tester::$useColors
- BC break: removed (virtual) method Job::onAfterExecute
- BC break: removed BaseAttribute
- fixed failure line in JUnit reports
- BC break: replaced method IResultsFormatter::render with outputResults
- BC break: only array can be passed to attribute Skip (if any value is passed)
- improved error message for assertions assertSame and assertNotSame when any passed value is boolean
- BC break: replaced method TAssertions::showStringOrArray with showValue (the latter accepts value of any type)
- allowing skipping tests based on OS family with default skip checker
- BC break: renamed TestsStartedEvent to TestsStarted and TestsFinishedEvent to TestsFinished
- BC break: made Job::getSkip() protected
- BC break: tester extensions now define methods that are called directly instead of providing a list of callbacks, added methods for test case started/finished events

Version 6.1.0
- added CodeCoverageExtension, Reader, IAnnotationsReaderEngine, PhpAttributesEngine, TAssertions::getCounter(), TestsStartedEvent, TestsFinishedEvent to public api
- added TCompiledContainer, ContainerFactory and TComponent

Version 6.0.1
- Cobertura code coverage report now shows coverage in traits
- fixed invalid placement of lines for all functions in Cobertura code coverage report

Version 6.0.0
- first assertion failure in a test method now ends the method's execution
- raised minimal version of PHP to 8.3
- improved output of results formatter TextDox in console
- BC break: removed parameter $totalTime of IResultsFormatter::reportTestsFinished()
- BC break: removed Tester::$suites
- BC break: removed events onExecute and onFinish from Tester, use extensions instead

Version 5.1.0
- added assertions assertNoException and assertMatchesFile
- Job reports total elapsed time in milliseconds in $totalTime
- added the option to output results of tests in a different format to automated tests runner
- added argument --version to script mytester.php
- added option to change filename for some code coverage reports

Version 5.0.0
- BC break: forbidden passing string to parameter $value of assertions assertCount and assertNotCount
- added option to skip test after it was started
- automated tests runner and Nette DI container extension can generate code coverage reports
- moved code coverage engines into namespace MyTester\CodeCoverage\Engines and formatters into MyTester\CodeCoverage\Formatters
- BC break: renamed constant MyTesterExtension::TAG to TAG_TEST
- added extensions for automated tests runner
- BC break: removed options onExecute and onFinish of Nette DI container extension (replaced by automated tests runner's extensions)

Version 4.2.0
- moved CodeCoverageException to namespace MyTester\CodeCoverage
- allowed passing class name (as string) or object to TestCase::getSuiteName()
- added assertions assertMatchesRegExp and assertArrayOfClass
- improved error messages

Version 4.1.0
- added assertions assertGreaterThan, assertLessThan and assertOutput
- moved interfaces ICodeCoverageEngine and ICodeCoverageFormatter to namespace MyTester\CodeCoverage
- moved interface IAnnotationsReaderEngine to namespace MyTester\Annotations
- added option to mark a test as incomplete
- deprecated passing string to parameter $value of assertions assertCount and assertNotCount

Version 4.0.0
- test methods that have at least 1 parameter but their data provider provides none are now skipped
- added event onFinish to Tester
- automated tests runner and Nette DIC extension can now report % of code coverage
- BC break: removed option to mark test method as supposed to fail
- added new assertion throwsException
- *.errors files from previous runs are now deleted in automated tests runner
- automated tests runner now reports number of passed tests
- test methods that do not perform any assertion are now reported as passed with warnings
- raised minimal version of PHP to 8.2
- possible BC break: made some properties/classes readonly
- BC break: job/test results are now implemented via enum MyTester\JobResult

Version 3.0.0
- BC break: removed support for *.phpt files
- BC break: removed Assert, Environment
- BC break: removed data and testSuit annotations
- allowed customization of finding test suites in automated tests runner
- BC break: moved PHP attributes to namespace MyTester\Attributes
- automated tests runner now uses Composer's autoloader, package nette/robot-loader is optional
- BC break: marked Job as final
- automated tests runner's output can be colorized
- dropped support for Composer 1
- raised minimal version of PHP to 8.0
- BC break: removed support for phpDoc annotations

Version 2.1.0
- allowed customization of test suite creation in automated tests runner
- made Job::$name, Job::$params and Job::$shouldFail readable
- deprecated Environment
- possible BC break: only public methods in TestCase whose name starts with *test* are now considered tests
- added @dataProvider annotation, it should be used instead of data
- possible BC break: renamed method TestCase::getSuitName() to getSuiteName() and Tester::$suits to $suites
- deprecated annotation @testSuit in favor of new @testSuite

Version 2.0.1
- changed default value for attributes to true
- added attributes to public api

Version 2.0.0
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
- BC break: marked methods Environment::incCounter(), Environment::resetCounter(), Environment::addSkipped() as internal/private
- automated tests runner now supports *Test.php files, they should be used instead of *.phpt files
- BC break: marked TestsRunner as internal
- removed support for running from browser
- PHP 8 attributes can be used instead of phpDoc annotations

Version 1.1
- the environment for Nette DIC extension is now set up in TestsRunner::execute()
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
- BC break: TestCase::getJobs() now returns array of Job
- first parameter of Runner::addJob() can be an instance of Job now
- added methods startUp and shutDown to TestCase which are called at start/end of the suite
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
- test suites can have custom names
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
