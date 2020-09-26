My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://github.com/konecnyjakub/mytester/releases) [![Latest Unstable Version](https://poser.pugx.org/konecnyjakub/mytester/v/unstable)](https://packagist.org/packages/konecnyjakub/mytester) [![Build Status](https://travis-ci.org/konecnyjakub/mytester.svg?branch=master)](https://travis-ci.org/konecnyjakub/mytester) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://github.com/konecnyjakub/mytester/blob/master/LICENSE) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/konecnyjakub/mytester/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/konecnyjakub/mytester/?branch=master)

My Tester allows to run simple tests. Requires PHP 7.4 or later.

Installation
------------
The best way to install My Tester is via Composer. Just add konecnyjakub/mytester to your (dev) dependencies.

Usage
-----

### Setting up

Firstly, you have to include My Tester's files and set up environment for testing.

```php
<?php
declare(strict_types=1);

require "path_to_your_project/vendor/autoload.php";

MyTester\Environment::setup();
?>
```

### Tests

After you've set the environment, you can do your tests. For various comparisons, there is prepared class Assert with static methods. They automatically print the results. Some examples (hopefully self explaining):
```php
<?php
declare(strict_types=1);

use MyTester\Assert;

Assert::same("abc", $result);
Assert::true(someCondition);
Assert::count(5, $array);
Assert::type("string", $string);
?>
```
. It is also possible to run custom assertions with Assert::tryAssertion().

### Test Case

It is also possible to use object-oriented style to make tests. Create a class extending MyTester\TestCase. All its methods which name starts with "test" will be automatically launched when you call method "run". Call methods assert*Something* inside them. An example:
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

$suit = new Tests();
$suit->run();
?>
```

#### Parameters for test methods

Test methods of TestCase descendants can take one parameter. Its value is taken from annotation @data. It can be a list of value, in that case the method will be run multiple time, every time with one value from the list. Example:
```php
<?php
declare(strict_types=1);

class Tests extends MyTester\TestCase {
  /**
   * @data(abc, adef)
   */
  public function testParams(string $text): void {
    $this->assertContains("a", $text);
  }
}
?>
```

#### Custom names for tests

You can give test methods and whole test suits custom names that will be displayed in the output instead of standard NameOfClass::nameOfMethod. It is done via documentation comment @test/@testSuit. Example:
```php
<?php
declare(strict_types=1);

/**
 * @testSuit MyTests
*/
class Tests extends MyTester\TestCase {
  /**
   * @test Custom name
   */
  public function testTestName(): void {
    $this->assertTrue(1);
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
    $this->assertTrue(0);
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
    $this->assertTrue(0);
  }
}
?>
```

#### Setup and clean up

If you need to do some things before/after each test in TestCase, you can define methods setUp/tearDown. And if you define methods startUp/shutDown, they will be automatically called at start/end of suit.

Automated tests runner
----------------------

It is possible to use automated tests runner that will scan specified folder for *Test.php and *.phpt files and run their TestCases (described in section Test Case). An example of usage:
```php
<?php
declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";
$folder = __DIR__ . "/tests";

$tester = new MyTester\Tester($folder);
$tester->execute();
?>
```

The automated tests runner needs package nette/robot-loader.

You may also use prepared script *./vendor/bin/mytester*. It will use folder your_project_root/tests, but you can specify any folder as its first argument:

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
Then you get service named mytester.runner (of type MyTester\Bridges\NetteDI\TestsRunner) from the container and run its method execute. It returns FALSE if all tests passed else TRUE. You can use it (after turning to integer) as exit code of your script: 

```php
<?php
declare(strict_types=1);

$result = $container->getService("mytester.runner")->execute(); //or
$result = $container->getByType(MyTester\Bridges\NetteDI\TestsRunner::class)->execute();
exit((int) $result);
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
