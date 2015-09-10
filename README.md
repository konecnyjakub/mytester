My Tester
=========

My Tester allows to run simple tests. Requires PHP 5.4 or later.

Installation
------------
There are currently 3 ways to install My Tester.

1. Download an archive from the repository. It's best to donwload archive of latest tagged verson as it's considered stable. But you can try out master branch if you want to.
2. It's also possible to use phar archive. Phar archives for all versions are in [GitHub repository](https://github.com/konecnyjakub/mytester). Alternatively you can download/fork the repository and run create_phar.php script to obtain it.
3. Use composer to obtain it. Just add konecnyjakub/mystester to your (dev)dependencies.

### Dependencies
My Tester has some dependencies that are neither included in the repository nor in the phar archive (though some of them are optional). You have to either install them manually or use composer  to obtain them. In manual installation you have to tell your scripts where to look for them.

Usage
-----
### Setting up
Firstly, you have to include My Tester's bootstrap and set up environement for testing.

```php
require "path_to_mytester/src/bootstrap.php"; // if you downloaded archive of repository
require "path_to_mytester/mytester.phar"; // or if you have phar archive
MyTester\Environment::setup();
```
By default, the output is printed in browser/console. If you want to save it to a file, use this:
```php
MyTester\Environment::setup("file");
```
. The name of created file(s) will be displayed.

### Tests
After you've set the environment, you can do your tests. For various comparisons, there is prepared class Assert with static methods. They automatically print the results. Some examples(hopefully self exlaining) :
```php
use MyTester\Assert;

Asssert::same("abc", $result);
Assert::true(someCondition);
Assert::count(5, $array);
Assert::type("string", $string);
```
.

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
Test methods of TestCase descendants can take global variables as their parameters.

#### Custom names for tests
You can give test methods custom names that will be displayed in the output instead of standart NameOfClass::nameOfMethod. It is done via documentation comment @test. Example:
```php
class Tests extends MyTester\TestCase {
  /**
   * @test Custom name
   */
  function testTestName() {
    Assert::true(1);
  }
}
```

Automated tests runner
----------------------
It is possible to use automated tests runner that will scan specified folder for .phpt files and run their TestCases (described in section Test Case). An example of usage:
```php
require __DIR__ . "/vendor/autoload.php";
$folder = dirname(__DIR__ . "/tests");
$output = "screen";

MyTester\Environment::setup($output);

$tester = new MyTester\Tester($folder);
$tester->execute();
```
The automaded tests runner needs package nette/robot-loader.

More examples
-------------
For more examples of usage, see included tests of My Tester (in folder tests).
