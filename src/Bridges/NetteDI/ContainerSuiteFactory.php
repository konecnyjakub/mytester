<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\TestCase;
use Nette\DI\Container;
use RuntimeException;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ContainerSuiteFactory implements \MyTester\ITestSuiteFactory {
  private Container $container;

  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function create(string $className): TestCase {
    $suit = $this->container->getByType($className);
    if(!$suit instanceof TestCase) {
      throw new RuntimeException("$className is not a descendant of " . TestCase::class . ".");
    }
    return $suit;
  }
}
?>