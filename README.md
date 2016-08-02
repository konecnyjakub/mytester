My Tester
=========

[![Total Downloads](https://poser.pugx.org/konecnyjakub/mytester/downloads)](https://packagist.org/packages/konecnyjakub/mytester) [![Latest Stable Version](https://poser.pugx.org/konecnyjakub/mytester/v/stable)](https://github.com/konecnyjakub/mytester/releases) [![Latest Unstable Version](https://poser.pugx.org/konecnyjakub/mytester/v/unstable)](https://packagist.org/packages/konecnyjakub/mytester) [![Build Status](https://travis-ci.org/konecnyjakub/mytester.svg?branch=master)](https://travis-ci.org/konecnyjakub/mytester) [![License](https://poser.pugx.org/konecnyjakub/mytester/license)](https://github.com/konecnyjakub/mytester/blob/master/LICENSE)

My Tester allows to run simple tests. Requires PHP 5.6 or later.

Installation
------------
The best way to install My Tester is via Composer. Just add konecnyjakub/mytester to your (dev) dependencies.

Usage
-----
### Setting up
Firstly, you have to include My Tester's files and set up environement for testing.

```php
require "path_to_your_project/vendor/autoload.php";
MyTester\Environment::setup();
```
By default, the output is printed in browser/console. If you want to save it to a file, use this:
```php
MyTester\Environment::setup("file");
```
. The name of created file(s) will be displayed.

### Tests
After you've set the environment, you can do your tests. For various comparisons, there is prepared class Assert with static methods. They automatically print the results. Some examples (hopefully self exlaining):
```php
use MyTester\Assert;

Asssert::same("abc", $result);
Assert::true(someCondition);
Assert::count(5, $array);
Assert::type("string", $string);
```
. It is also possible to run custom assertions with Assert::tryAssertion().

### Test Case
It is also possible to use object-oriented style to make tests. Create a class extending MyTester\TestCase. All its methods which name starts with "test" will be automaticaly launched when you call method "run". An example:
```php
class Tests extends MyTester\TestCase {
  function testA() {
    $actual = someCall();
    $text = anotherCall();
    Assert::same("abc", $actual);
    Assert::same("def", $text);
  }
}

$suit = new Tests();
$suit->run();
```

#### Parameters for test methods
Test methods of TestCase descendants can take one parameter. Its value is taken from annotation @data. It can be a list of value, in that case the method will be run multiple time, every time with one value from the list. Example:
```php
class Tests extends MyTester\TestCase {
  /**
   * @param string $text
   * @data(abc, adef)   
   */
  function testParams($text) {
    Assert::contains("a", $text);
  }
}
```

#### Custom names for tests
You can give test methods and whole test suits custom names that will be displayed in the output instead of standart NameOfClass::nameOfMethod. It is done via documentation comment @test/@testSuit. Example:
```php
/**
 * @testSuit MyTests
*/
class Tests extends MyTester\TestCase {
  /**
   * @test Custom name
   */
  function testTestName() {
    Assert::true(1);
  }
}
```

#### Skipping tests
It is possible to unconditionally skip a test. Just add documentation comment @skip. Example:
```php
class Tests extends MyTester\TestCase {
  /**
   * @skip
   */
  function testTestName() {
    Assert::true(1);
  }
}
```
. You can also add conditions where the test should be skipped. Simple values like numbers, strings and boolean are evaluated directly. If you provide an array, all keys and their values are checked. One supported key is "php". If your version of PHP is lesser than its value, the test is skipped. You can also use key "extension" where the test will be skipped when that extension is not loaded. Skipped tests are shown in output. Examples:
```php
class Tests extends MyTester\TestCase {
  /**
   * @skip(1)
   * @skip(true)
   * @skip(abc)
   * @skip(php=5.4.1)
   * @skip(extension=abc)
   */
  function testTestName() {
    Assert::true(1);
  }
}
```

#### Setup and clean up
If you need to do some things before/after each test in TestCase, you can define methods setUp/tearDown. And if you define methods startUp/shutDown, they will be automatically called at start/end of suit.

Automated tests runner
----------------------
It is possible to use automated tests runner that will scan specified folder for .phpt files and run their TestCases (described in section Test Case). An example of usage:
```php
require __DIR__ . "/vendor/autoload.php";
$folder = __DIR__ . "/tests";

$tester = new MyTester\Tester($folder);
$tester->execute();
```
The automaded tests runner needs package nette/robot-loader.

Nette applications
------------------
If you are developing a Nette application, you may want to use our extension for Nette DI container. It combines automated tests runner with powers of dependency injection. In other words, it automatically runs your test cases and passed them their dependencies from DI container. Its usage is simple, just add these lines to your config file:
```
extensions:
    mytester: MyTester\Bridges\NetteDI\MyTesterExtension
```
The extension expects your test cases to be place in your_project_root/tests. If there are in a different folder, you have to add folder paramter to the extension:
```
mytester:
    folder: %wwwDir%/tests
```
.

More examples
-------------
For more examples of usage, see included tests of My Tester (in folder tests).
