My Tester
=========

My Tester allows to run simple tests. Requires PHP 5.4 or later.

Installation
------------
There are currently 2 ways to install My Tester.

1. Download an archive from the repository. It's best to donwload archive of latest tagged verson as it's considered stable. But you can try out master branch if you want to.
2. It's also possible to use phar archive. At the moment, phar archives aren't published anywhere, you have to download/fork the repository and run create_phar.php script to obtain it.

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
To be added.

For more examples of usage see included tests of My Tester (in folder tests).
