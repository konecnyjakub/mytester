My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://gitlab.com/konecnyjakub/mytester/-/releases) [![build status](https://gitlab.com/konecnyjakub/mytester/badges/master/pipeline.svg?ignore_skipped=true)](https://gitlab.com/konecnyjakub/mytester/-/commits/master) [![coverage report](https://gitlab.com/konecnyjakub/mytester/badges/master/coverage.svg)](https://gitlab.com/konecnyjakub/mytester/-/commits/master) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://gitlab.com/konecnyjakub/mytester/-/blob/master/LICENSE.md)

My Tester is an adaptable and extensible testing framework for (and in) PHP. It requires PHP 8.3 or later and Composer 2.2 or later.

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

final class Tests extends MyTester\TestCase
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

Test methods of TestCase descendants can take one or more parameters. You can provide a name of a public method from the class which returns an array or an iterable object with DataProvider attribute. Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\DataProvider;

final class Tests extends MyTester\TestCase
{
    #[DataProvider("dataProvider")]
    public function testParams(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }
    
    #[DataProvider("dataProviderIterator")]
    public function testParamsIterator(string $text, int $number):void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }

    public function dataProvider(): array
    {
        return [
            ["abc", 1, ],
            ["abcd", 2, ],
        ];
    }

    public function dataProviderIterator(): iterable
    {
        yield ["abc", 1, ];
        yield ["abcd", 2, ];
    }
}
```

In the example both test methods will be run 2 times, first time with parameters "abc" a 1, second time with "abcd" and 2.

If a test method with data set fails, the faulty data set is shown along the test method name. It is possible to name a data set so the name is shown instead of the whole data, it is done by providing a string index to the element.

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\DataProvider;

final class Tests extends MyTester\TestCase
{
    #[DataProvider("dataProvider")]
    public function testParams(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }

    public function dataProvider(): array
    {
        return [
            "first" => ["abc", 1, ],
            "second" => ["abcd", 2, ],
        ];
    }
}
```

If you want to use a static method from a different class as data provider, use attribute DataProviderExternal instead. It takes 2 parameters: class name and method name, otherwise it works just like DataProvider.

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\DataProviderExternal;

final class ExternalDataProvider
{
    public static function dataProviderArray(): array
    {
        return [
            "first" => ["abc", 1, ],
            ["abcd", 2, ],
        ];
    }
}


final class Tests extends \MyTester\TestCase {
    #[DataProviderExternal(ExternalDataProvider::class, "dataProviderArray")]
    public function testParams(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }
}
```

If you do not want to define a method which returns the data sets, you can just use attribute Data. Unlike other similar attributes it can be used multiple times on a method and each instance define 1 data set. But it is not possible to name data sets this way. Example:


```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Data;

final class Tests extends \MyTester\TestCase {
    #[Data(["abc", 1, ]]
    #[Data(["abcd", 2, ])]
    public function testParams(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }
}
```

It is not possible to use different attribute for specifying a data provider together, only one type is used. Their priority is Data first, DataProvider second and DataProviderExternal third.

#### Custom names for tests

You can give test methods and whole test suites custom names that will be displayed in the output instead of standard NameOfClass::nameOfMethod. It is done via attribute Test/TestSuite. Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Test;
use MyTester\Attributes\TestSuite;

#[TestSuite("MyTests")]
final class Tests extends MyTester\TestCase
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

final class Tests extends MyTester\TestCase
{
    #[Skip()]
    public function testTestName(): void
    {
        $this->assertTrue(false);
    }
}
```

. You can also add conditions when the test should be skipped. For that you need to use a specific attribute. One supported is RequiresPhpVersion, if your version of PHP is lesser than its value, the test is skipped. You can also use attribute RequiresPhpExtension where the test will be skipped when that extension is not loaded. If you use attribute RequiresSapi, the test will not be executed if the current sapi is different. With attribute RequiresOsFamily, you can skip a test if tests are run on a different OS family (taken from constant PHP_OS_FAMILY). With attribute RequiresPackage you can skip a test if a Composer package is not installed; if you have installed package composer/semver, you can also pass a version constraint as second parameter. Attribute RequiresEnvVariable allows skipping a test if an env variable is not set, optionally you can also provide value that it should have. Skipped tests are shown in output. Examples:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\RequiresOsFamily;
use MyTester\Attributes\RequiresPackage;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\RequiresPhpExtension;
use MyTester\Attributes\RequiresSapi;

final class Tests extends MyTester\TestCase
{
    #[RequiresPhpVersion("5.4.1")]
    #[RequiresPhpExtension("abc")]
    #[RequiresSapi("cgi")]
    #[RequiresOsFamily("Solaris")]
    #[RequiresPackage("phpstan/phpstan")]
    #[RequiresEnvVariable("TEST")]
    public function testTestName(): void
    {
        $this->assertTrue(false);
    }
}
```

Attributes RequiresPhpExtension and RequiresEnvVariable can be used multiple times on one method/class. If multiple conditions are provided, all have to be met, otherwise the test will be skipped.

If the condition is too complicated (or you don't want to use an attribute for any reason), use can call method markTestSkipped from the test method. It optionally accepts a message explaining why it is skipped.

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;

final class Tests extends MyTester\TestCase
{
    public function testTestName(): void
    {
        $this->markTestSkipped("Optional message");
        $this->assertTrue(false);
    }
}
```

If you want to skip all test methods in a test suite (both unconditionally and based on conditions), just use the above mentioned attributes on the class.

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\Skip;

#[Skip]
final class Tests extends MyTester\TestCase
{
    public function testTestName(): void
    {
        $this->assertTrue(false);
    }
}
```

#### Incomplete tests

If a test is not completely written yet, you can mark it as incomplete and it will be passed with warning. Just call method markTestIncomplete. You can optionally pass it a message explaining why it is incomplete. Once the method is called, no other assertions will be performed in the method where it is called.

```php
<?php
declare(strict_types=1);

final class Tests extends MyTester\TestCase
{
    public function testIncomplete(): void
    {
        $this->assertTrue(true);
        $this->markTestIncomplete("Optional message");
    }
}
```
#### Tests without assertions

By default, if a test method performs no assertions, it is reported as passed with warnings. If you do not set a different results formatter (see below), it will print a warning `Method name passed with warning: No assertions were performed.`. It is possible to suppress that warning by adding attribute NoAssertions on a test method or the whole class, then it is reported as passed (assuming there are no other issues). Example:

```php
<?php
declare(strict_types=1);

use MyTester\Attributes\NoAssertions;

final class Tests extends MyTester\TestCase
{
    #[NoAssertions]
    public function testNoAssertions(): void
    {
    }
}
```

#### Unexpected errors/exceptions

If an unexpected Error or Exception is thrown, it is reported as a failure for that test method. If you expect some test code to cause an error/exception and want that, you can just use method assertThrowsException. Conversely if no exception/error should be caused by code, you can test it with method assertNoException.

#### Deprecations

If a test method triggers deprecation, it is reported as a warning for the test method. It can be caused by calling function **trigger_error** with level _E_USER_DEPRECATED_ or on PHP 8.4 and later by using anything that is marked with attribute **Deprecated**, e. g. calling a method/function or using a class constant.

It is possible to check that code in a test method triggers a deprecation with method assertTriggersDeprecation, you can even check for a specific message; in that case it is not reported by My Tester (but still might be by other tools that check your code, e. g. PHPStan with deprecation rules). Conversely with method assertTriggersNoDeprecation you can check that code triggers no deprecation.

If you do not want to have deprecations reported in a test method or a whole TestCase (because you are e. g. deliberately testing deprecated code), you can suppress those warnings if you add attribute MyTester\Attributes\IgnoreDeprecations to the method/class. Then if the test method successfully completes without any other warnings/errors/assertion failures, it is reported as passed.

#### Setup and clean up

If you need to do some things before/after each test in TestCase, create a non-static public method in the class and add attribute MyTester\Attributes\BeforeTest or MyTester\Attributes\AfterTest. And if you add attribute MyTester\Attributes\BeforeTestSuite or MyTester\Attributes\AfterTestSuite, they will be automatically called at start/end of suite.

Running tests
-------------

The easiest way to run your test cases is to use the provided script *vendor/bin/mytester*. It scans folder *your_project_root/tests* (by default) for *Test.php files and runs TestCases in them. You can tell it to use a different folder by specifying it as the script's first argument:

```bash
./vendor/bin/mytester tests/unit
```

If you have correctly configured Composer to autoload your test suites and use optimized autoloader, you are all set. If Composer cannot find them, install package nette/robot-loader and it will be used to find and load them.

By default, all test suites found in the folder are run but it is possible to choose only some to run. One way is through groups, you assign a test suite to one or more groups and then name the group(s) that should be run or omitted. Examples:

```php
declare(strict_types=1);

use MyTester\Attributes\Group;

#[Group("one")]
#[Group("abc")]
final class FirstTest extends \MyTester\TestCase
{
}

#[Group("two")]
#[Group("abc")]
final class SecondTest extends \MyTester\TestCase
{
}

#[Group("def")]
final class ThirdTest extends \MyTester\TestCase
{
}
```

```bash
./vendor/bin/mytester tests/unit --filterOnlyGroups one # only FirstTest is run
./vendor/bin/mytester tests/unit --filterOnlyGroups abc # only FirstTest and SecondTest are run
./vendor/bin/mytester tests/unit --filterOnlyGroups one,two # only FirstTest and SecondTest are run
./vendor/bin/mytester tests/unit --filterExceptGroups def # only FirstTest and SecondTest are run
./vendor/bin/mytester tests/unit --filterExceptGroups one,two # only ThirdTest is run
```

It is also possible to exclude test suites from specified subfolders. This is done via argument *--filterExceptFolders*; it can be passed multiple times and each value is interpreted as subfolder of the provided or guessed base folder.

```bash
./vendor/bin/mytester tests/unit --filterExceptFolders feature1
```

### PHPT tests

Automated tests runner can also run .phpt files in the provided folder, you just need to add package *konecnyjakub/phpt-runner* to your (dev) dependencies. If you want to (temporarily) disable .phpt tests, pass *--noPhpt* to the script. It is not possible to disable tests in specific subfolders with *--filterExceptFolders* at the moment.

All .phpt files are considered one test suite, each file is reported as a test just like a method in a TestCase descendant.

For description of the file format, see https://php.github.io/php-src/miscellaneous/writing-tests.html.

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

My Tester automatically generates report % of code coverage when possible. It is done in class MyTester\Tester, so it is available in the provided script *vendor/bin/mytester* and our extension for Nette DI container (see below). You just need to run the script with pcov or (alternatively) xdebug extension enabled. pcov is highly recommended as it was created specifically for this purpose and does not have the overhead of a debugger. 

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

Automated tests runner's functionality can be extended by extensions. Extensions have to implement the *MyTester\ITesterExtension* interface, they can add listeners to events that are fired during automated tests runner's life cycle. Listeners are set in method getSubscribedEvents, it is possible to add them only for events that are necessary. Every event has a class in namespace MyTester\Events. For now, custom extensions cannot be registered when using the script *vendor/bin/mytester*.

Some automated tests runner's functionality (printing My Tester and PHP version, generating code coverage reports, saving errors into files) is actually implemented via extensions. They have to be added manually which means that custom scripts do not have use that functionality (and do not by default). The script *vendor/bin/mytester* and Nette DI container (see below) extension have all of them enabled.

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

You can also run test suites only from named groups or run all test suites except those in named groups.

```neon
mytester:
    filterOnlyGroups:
        - one
        - two
    filterExceptGroups:
        - abc
        - def
```

. If you want to exclude test suite from certain subfolders of the base folder, just use parameter filterExceptFolders.

```neon
mytester:
    filterExceptFolders:
        - feature1
        - feature3
```

If you need to do some tasks before/after your tests, you can use automated tests runner extensions. Just register them with option extensions.

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

final class Tests extends MyTester\TestCase
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

final class Tests extends MyTester\TestCase
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

final class Tests extends MyTester\TestCase
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
