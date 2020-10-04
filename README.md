My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://github.com/konecnyjakub/mytester/releases) [![Latest Unstable Version](https://poser.pugx.org/konecnyjakub/mytester/v/unstable)](https://packagist.org/packages/konecnyjakub/mytester) [![Build Status](https://travis-ci.org/konecnyjakub/mytester.svg?branch=master)](https://travis-ci.org/konecnyjakub/mytester) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://github.com/konecnyjakub/mytester/blob/master/LICENSE) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/konecnyjakub/mytester/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/konecnyjakub/mytester/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/konecnyjakub/mytester/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/konecnyjakub/mytester/?branch=master)

My Tester allows to run simple tests. Requires PHP 7.4 or later.

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

class Tests extends MyTester\TestCase {
  public function testA(): void {
    $actual = someCall();
    $text = anotherCall();
    $this->assertSame("abc", $actual);
    $this->assertSame("def", $text);
  }
}

$suite = new Tests();
$suite->run();
?>
```

#### Parameters for test methods

Test methods of TestCase descendants can take one parameter. You can provide a name of a public method from the class which returns an array with @dataProvider annotation. It can be a list of value, in that case the method will be run multiple time, every time with one value from the list. Example:
```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase {
  /**
   * @dataProvider(dataProvider)
   */
  public function testParams(string $text): void {
    $this->assertContains("a", $text);
  }

  public function dataProvider(): array {
    return [
      ["abc", "def"],
    ];
  }
}
?>
```

#### Custom names for tests

You can give test methods and whole test suites custom names that will be displayed in the output instead of standard NameOfClass::nameOfMethod. It is done via documentation comment @test/@testSuite. Example:
```php
<?php
declare(strict_types=1);

/**
 * @testSuite MyTests
*/
class Tests extends MyTester\TestCase {
  /**
   * @test Custom name
   */
  public function testTestName(): void {
    $this->assertTrue(true);
  }
}
?>
```

#### Skipping tests

It is possible to unconditionally skip a test. Just add documentation comment @skip. Example:
```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase {
  /**
   * @skip
   */
  public function testTestName(): void {
    $this->assertTrue(false);
  }
}?>
```

. You can also add conditions where the test should be skipped. Simple values like numbers, strings and boolean are evaluated directly. If you provide an array, all keys and their values are checked. One supported key is "php". If your version of PHP is lesser than its value, the test is skipped. You can also use key "extension" where the test will be skipped when that extension is not loaded. If you use sapi key, the test will not be executed if the current sapi is different. Skipped tests are shown in output. Examples:
```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase {
  /**
   * @skip(1)
   * @skip(true)
   * @skip(abc)
   * @skip(php=5.4.1)
   * @skip(extension=abc)
   * @skip(sapi=cgi)
   */
  public function testTestName(): void {
    $this->assertTrue(false);
  }
}
?>
```

#### Annotations style

In all previous examples you have seen annotations in style:

```php
/**
* @testSuite Abc
* @skip(abc)
*/
```

but that is not the only way. If you use PHP 8, you can also make use of attributes. All used attributes are in namespace MyTester\Annotations\Attributes. Examples:

```php
<?php
declare(strict_types=1);

use MyTester\Annotations\Attributes\DataProvider;
use MyTester\Annotations\Attributes\Skip;
use MyTester\Annotations\Attributes\Fail;
use MyTester\Annotations\Attributes\Test;
use MyTester\Annotations\Attributes\TestSuite;

#[TestSuite("Abc")]
class AbcTest extends MyTester\TestCase {
  
  #[Test("Abc")]
  public function testOne(): void {
    $this->assertTrue(true);
  }

  #[Skip()]
  public function testSkip(): void {
    $this->assertTrue(false);
  }

  #[Fail()]
  public function testFail(): void {
    $this->assertTrue(false);
  }

  #[DataProvider("dataProvider")]
  public function testParams(string $text): void {
    $this->assertContains("a", $text);
  }

  public function dataProvider(): array {
    return [
      ["abc", "def"],
    ];
  }
}
?>
```

#### Setup and clean up

If you need to do some things before/after each test in TestCase, you can define methods setUp/tearDown. And if you define methods startUp/shutDown, they will be automatically called at start/end of suite.

Running tests
-------------

The easiest way to run your test cases is to use the provided script *vendor/bin/mytester*. It scans folder your_project_root/tests (by default) for *Test.php files and runs TestCases in them. The script requires package nette/robot-loader. You can tell it to use a different folder by specifying it as the script's first argument:

```bash
./vendor/bin/mytester tests/unit
```

Nette applications
------------------

If you are developing a Nette application, you may want to use our extension for Nette DI container. It combines automated tests runner with powers of dependency injection. In other words, it automatically runs your test cases and passed them their dependencies from DI container. Its usage is simple, just add these lines to your config file:

```neon
extensions:
    mytester: MyTester\Bridges\NetteDI\MyTesterExtension
```
Then you get service named mytester.runner (of type MyTester\Tester) from the container and run its method execute. It automatically ends the script with 0/1 depending on whether all tests passed.

```php
<?php
declare(strict_types=1);

$result = $container->getService("mytester.runner")->execute(); //or
$container->getByType(MyTester\Tester::class)->execute();
?>
```

The extension expects your test cases to be place in your_project_root/tests. If there are in a different folder, you have to add folder parameter to the extension:

```neon
mytester:
    folder: %wwwDir%/tests
```

. And if you need to do some tasks before your tests, you can use option onExecute. It is an array of callbacks. Examples:

```neon
mytester:
    onExecute:
        - Class::staticMethod
        - [@service, method]
        - [Class, staticMethod]
```

More examples
-------------

For more examples of usage, see included tests of My Tester (in folder tests).
