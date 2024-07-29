My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://gitlab.com/konecnyjakub/mytester/-/releases) [![build status](https://gitlab.com/konecnyjakub/mytester/badges/master/pipeline.svg)](https://gitlab.com/konecnyjakub/mytester/commits/master) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://gitlab.com/konecnyjakub/mytester/-/blob/master/LICENSE.md) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/konecnyjakub/mytester/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/konecnyjakub/mytester/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/konecnyjakub/mytester/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/konecnyjakub/mytester/?branch=master)

My Tester allows to run simple tests. Requires PHP 8.2 or later and Composer 2.

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

. You can also add conditions where the test should be skipped. Simple values like numbers, strings and boolean are evaluated directly. If you provide an array, all keys and their values are checked. One supported key is "php". If your version of PHP is lesser than its value, the test is skipped. You can also use key "extension" where the test will be skipped when that extension is not loaded. If you use sapi key, the test will not be executed if the current sapi is different. Skipped tests are shown in output. Examples:
```php
<?php

declare(strict_types=1);

use MyTester\Attributes\Skip;

class Tests extends MyTester\TestCase
{
    #[Skip(1)]
    #[Skip(true)]
    #[Skip("abc")]
    #[Skip(["php" => "5.4.1"])]
    #[Skip(["extension" => "abc"])]
    #[Skip(["sapi" => "cgi"])]
    public function testTestName(): void
    {
        $this->assertTrue(false);
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

Automated tests runner can print results with colors, but it is not enabled but default. To use colors just pass argument *--colors* to the script.

```bash
./vendor/bin/mytester tests/unit --colors
```

### Code coverage

My Tester is able to report % of code coverage. It is done in class MyTester\Tester, so it is available in the provided script *vendor/bin/mytester* and our extension for Nette DI container (see below). You just need to run the script with pcov or xdebug extension enabled or with phpdbg binary of php.

But it is not able to generate full code coverage reports yet. Before it is supported natively, we recommend using package [phpunit/php-code-coverage](https://packagist.org/packages/phpunit/php-code-coverage) and a custom script for running tests. Example:

```php
<?php

declare(strict_types=1);

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\FileIterator\Facade;

require __DIR__ . "/vendor/autoload.php";

$filter = new Filter();
$filter->includeFiles((new Facade())->getFilesAsArray(__DIR__ . "/src", ".php"));

$coverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
$coverage->start("My Tester");
register_shutdown_function(function() use ($coverage) {
  $coverage->stop();
  (new Clover())->process($coverage, __DIR__ . "/coverage.xml");
});

require __DIR__ . "/vendor/bin/mytester.php";

```

Nette applications
------------------

If you are developing a Nette application, you may want to use our extension for Nette DI container. It combines automated tests runner with powers of dependency injection. In other words, it automatically runs your test cases and passed them their dependencies from DI container. Its usage is simple, just add these lines to your config file:

```neon
extensions:
    mytester: MyTester\Bridges\NetteDI\MyTesterExtension
```
Then you get service named **mytester.runner** (of type MyTester\Tester) from the container and run its method execute. It automatically ends the script with 0/1 depending on whether all tests passed.

```php
<?php

declare(strict_types=1);

$result = $container->getService("mytester.runner")->execute(); //or
$container->getByType(MyTester\Tester::class)->execute();

```

The extension expects your test cases to be place in *your_project_root/tests*. If they are in a different folder, you have to add folder parameter to the extension:

```neon
mytester:
    folder: %wwwDir%/tests
```

. And if you need to do some tasks before/after your tests, you can use option onExecute/onFinish. It is an array of callbacks. Examples:

```neon
mytester:
    onExecute:
        - Class::staticMethod
        - [@service, method]
        - [Class, staticMethod]
    onFinish:
        - Class::staticMethod
        - [@service, method]
        - [Class, staticMethod]
```

Colors in output can be enabled by setting option colors to true:

```neon
mytester:
    colors: true
```

More examples
-------------

For more examples of usage, see included tests of My Tester (in folder tests).
