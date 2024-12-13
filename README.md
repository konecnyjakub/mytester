My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://gitlab.com/konecnyjakub/mytester/-/releases) [![build status](https://gitlab.com/konecnyjakub/mytester/badges/master/pipeline.svg?ignore_skipped=true)](https://gitlab.com/konecnyjakub/mytester/-/commits/master) [![coverage report](https://gitlab.com/konecnyjakub/mytester/badges/master/coverage.svg)](https://gitlab.com/konecnyjakub/mytester/-/commits/master) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://gitlab.com/konecnyjakub/mytester/-/blob/master/LICENSE.md)

My Tester is an adaptable and extensible testing framework for (and in) PHP. It requires PHP 8.3 or later and Composer 2.

Installation
------------

The best way to install My Tester is via Composer. Just add konecnyjakub/mytester to your (dev) dependencies.

Usage
-----

### Test Case

My Tester uses object-oriented style to define tests. Your classes with tests have to extend MyTester\TestCase. All its public methods which name starts with "test" will be automatically launched when you call method "run". Call methods assert*Something* inside them. An example:

```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase
{
    public function testA(): void
    {
        $actual = someCall();
        $text = anotherCall();
        $this->assertSame("abc", $actual);
        $this->assertSame("def", $text);
    }
}
```

#### Parameters for test methods

Test methods of TestCase descendants can take one parameter. You can provide a name of a public method from the class which returns an array with DataProvider attribute. It can be a list of value, in that case the method will be run multiple time, every time with one value from the list. Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\DataProvider;

class Tests extends MyTester\TestCase
{
    #[DataProvider("dataProvider")]
    public function testParams(string $text): void
    {
        $this->assertContains("a", $text);
    }

    public function dataProvider(): array
    {
        return [
            ["abc", "def"],
        ];
    }
}
```

#### Custom names for tests

You can give test methods and whole test suites custom names that will be displayed in the output instead of standard NameOfClass::nameOfMethod. It is done via attribute Test/TestSuite. Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Test;
use MyTester\Attributes\TestSuite;

#[TestSuite("MyTests")]
class Tests extends MyTester\TestCase
{
    #[Test("Custom name")]
    public function testTestName(): void
    {
        $this->assertTrue(true);
    }
}
```

#### Skipping tests

It is possible to unconditionally skip a test. Just use attribute Skip. Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;

class Tests extends MyTester\TestCase
{
    #[Skip()]
    public function testTestName(): void
    {
        $this->assertTrue(false);
    }
}
```

. You can also add conditions where the test should be skipped. They can be provided as an array, keys and their values are checked until one matches. One supported key is "php". If your version of PHP is lesser than its value, the test is skipped. You can also use key "extension" where the test will be skipped when that extension is not loaded. If you use sapi key, the test will not be executed if the current sapi is different. Skipped tests are shown in output. Examples:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;

class Tests extends MyTester\TestCase
{
    #[Skip(["php" => "5.4.1"])]
    #[Skip(["extension" => "abc"])]
    #[Skip(["sapi" => "cgi"])]
    public function testTestName(): void
    {
        $this->assertTrue(false);
    }
}
```

If the condition is too complicated (or you don't want to use an attribute for any reason), use can call method markTestSkipped from the test method. It optionally accepts a message explaining why it is skipped.

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;

class Tests extends MyTester\TestCase
{
    public function testTestName(): void
    {
        $this->markTestSkipped("Optional message");
        $this->assertTrue(false);
    }
}
```

#### Incomplete tests

If a test is not completely written yet, you can mark it as incomplete and it will be passed with warning. Just call method markTestIncomplete. You can optionally pass it a message explaining why it is incomplete. Once the method is called, no other assertions will be performed in the method where it is called.

```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase
{
    public function testIncomplete(): void
    {
        $this->assertTrue(true);
        $this->markTestIncomplete("Optional message");
    }
}
```

#### Setup and clean up

If you need to do some things before/after each test in TestCase, you can define methods setUp/tearDown. And if you define methods startUp/shutDown, they will be automatically called at start/end of suite.

Running tests
-------------

The easiest way to run your test cases is to use the provided script *vendor/bin/mytester*. It scans folder *your_project_root/tests* (by default) for *Test.php files and runs TestCases in them. You can tell it to use a different folder by specifying it as the script's first argument:

```bash
./vendor/bin/mytester tests/unit
```

If you have correctly configured Composer to autoload your test suites and use optimized autoloader, you are all set. If Composer cannot find them, install package nette/robot-loader and it will be used to find and load them.

### Colorized output

Automated tests runner can print results with colors, but it is not enabled by default. To use colors just pass argument *--colors* to the script.

```bash
./vendor/bin/mytester tests/unit --colors
```

### Results format

It is possible to display results of tests in a different format and for some formats even put them into a file that can be processed by your continuous integration system, just pass argument *--resultsFormat* to the script. Supported formats at the moment are JUnit, TAP and TextDox (value is name of the format lower cased).

JUnit prints the results into file junit.xml, TAP and TestBox by default show the results in console/terminal. TestDox uses custom names set by attributes TestSuite/Test if they are set, otherwise just class name and method name.

```bash
./vendor/bin/mytester tests/unit --resultsFormat junit
```

```bash
./vendor/bin/mytester tests/unit --resultsFormat tap
```

```bash
./vendor/bin/mytester tests/unit --resultsFormat testdox
```

If you want to change the file for output with format JUnit or want to print the results into a file with formats TAP and TestBox, use also argument *--resultsFile*.

```bash
./vendor/bin/mytester tests/unit --resultsFormat junit --resultsFile custom_name.xml
```

### Code coverage

My Tester automatically generates report % of code coverage when possible. It is done in class MyTester\Tester, so it is available in the provided script *vendor/bin/mytester* and our extension for Nette DI container (see below). You just need to run the script with pcov or xdebug extension enabled.

It is also able to generate full code coverage reports. Supported formats are Cobertura and text. Just pass argument *--coverageFormat* to the script, the value is generally the name of the format in lower case. Both of them put the report into a file, for Cobertura it is coverage.xml, for text coverage.txt.

```bash
./vendor/bin/mytester tests/unit --coverageFormat cobertura
```

```bash
./vendor/bin/mytester tests/unit --coverageFormat text
```

It is possible to change the name for output for formats Cobertura and text, just add argument *--coverageFile*.

```bash
./vendor/bin/mytester tests/unit --coverageFormat cobertura --coverageFile cobertura.xml
```

### Automated tests runner extensions

Automated tests runner's functionality can be extended by extensions. They can add callbacks for certain events. Extensions have to implement the *MyTester\ITesterExtension* interface. For now, custom extensions cannot be registered when using the script *vendor/bin/mytester*,

Method getEventsPreRun returns callbacks that are called before all tests are run (when we know which test cases should be run), it receives MyTester\Events\TestsStartedEvent as its first parameter.

Method getEventsAfterRun returns callbacks that are called after all tests were run, it receives MyTester\Events\TestsFinishedEvent as its first parameter.

Method getEventsBeforeTestCase returns callbacks that are called before a test case is run, it receives MyTester\Events\TestCaseStarted as its first parameter.

Method getEventsAfterTestCase returns callbacks that are called after all test cases were run, it receives MyTester\Events\TestCaseFinished as its first parameter.

Nette applications
------------------

If you are developing a Nette application, you may want to use our extension for Nette DI container. It combines automated tests runner with powers of dependency injection. In other words, it automatically runs your test cases and passed them their dependencies from DI container. Its usage is simple, just add these lines to your config file:

```neon
extensions:
    mytester: MyTester\Bridges\NetteDI\MyTesterExtension
```

Then you get service of type MyTester\Tester from the container and run its method execute. It automatically ends the script with 0/1 depending on whether all tests passed.

```php
<?php

declare(strict_types=1);

$container->getByType(MyTester\Tester::class)->execute();
```

The extension expects your test cases to be place in *your_project_root/tests*. If they are in a different folder, you have to add folder parameter to the extension:

```neon
mytester:
    folder: %wwwDir%/tests
```

. And if you need to do some tasks before/after your tests, you can use automated tests runner extensions. Just register them with option extensions.

```neon
mytester:
    extensions:
        - MyExtensionClass
```

Colors in output can be enabled by setting option colors to true:

```neon
mytester:
    colors: true
```

It is also possible to generate code coverage reports with the extension, just use setting coverageFormat. See section Code coverage for supported formats and values.

```neon
mytester:
    coverageFormat: cobertura
```

If no format is set, only the total percent of code coverage will be reported.

My Tester contains a few utilities that make testing Nette applications easier. You can get the DIC container or any service from it in your test cases with trait MyTester\Bridges\NetteDI\TCompiledContainer.

```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;
    public function testService(): void
    {
        $service = $this->getService(\App\Model\MyClass::class);
        $this->assertTrue($service->someMethod());
    }
}
```

You can also recreate the container with new config.

```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;
    public function testService(): void
    {
        $config = [...];
        $this->refreshContainer($config);
    }
}
```

You can also test output of your components (either against a string or contents of a file) or just verify that it can be attached to a container.

```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase
{
    use \MyTester\Bridges\NetteApplication\TComponent;
    public function testService(): void
    {
        $component = new \Nette\Application\UI\Component();
        $this->attachToPresenter($component);
        $this->assertRenderOutput($component, "<div>test</div>");
        $this->assertRenderOutputFile($component,  __DIR__ . "/component_output.txt");
    }
}
```

Using traits TCompiledContainer and TComponent requires setting up the container, see tests/NetteDI.php as an example.

More examples
-------------

For more examples of usage, see included tests of My Tester (in folder tests).
