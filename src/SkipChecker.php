<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use Nette\Utils\Arrays;

/**
 * SkipChecker
 *
 * @author Jakub Konečný
 * @internal
 */
final class SkipChecker {
  use \Nette\SmartObject;

  public const ANNOTATION_NAME = "skip";

  private Reader $annotationsReader;
  /** @var callable[] */
  private array $checkers = [];

  public function __construct(Reader $annotationsReader) {
    $this->annotationsReader = $annotationsReader;
    $this->addDefaultCheckers();
  }

  private function addDefaultCheckers(): void {
    $this->addChecker("php", [$this, "checkPhpVersion"]);
    $this->addChecker("extension", [$this, "checkLoadedExtension"]);
    $this->addChecker("sapi", [$this, "checkPhpSapi"]);
  }

  public function addChecker(string $name, callable $callback): void {
    $this->checkers[$name] = $callback;
  }

  /**
   * @return mixed
   */
  public function getSkipValue(string $class, string $method) {
    return $this->annotationsReader->getAnnotation(static::ANNOTATION_NAME, $class, $method);
  }

  /**
   * Check whether to skip a test method
   *
   * @return bool|string
   */
  public function shouldSkip(string $class, string $method) {
    $value = $this->getSkipValue($class, $method);
    if($value === null) {
      return false;
    } elseif(is_scalar($value)) {
      return (bool) $value;
    } elseif(is_iterable($value)) {
      foreach($value as $k => $v) {
        $checker = Arrays::get($this->checkers, $k, null);
        if($checker === null) {
          return false;
        }
        return $checker($v);
      }
    }
    return false;
  }

  /**
   * @param mixed $value
   */
  public function checkPhpVersion($value): ?string {
    if(version_compare(PHP_VERSION, (string) $value, "<")) {
      return "PHP version is lesser than $value";
    }
    return null;
  }

  /**
   * @param mixed $value
   */
  public function checkLoadedExtension($value): ?string {
    if(!extension_loaded($value)) {
      return "extension $value is not loaded";
    }
    return null;
  }

  /**
   * @param mixed $value
   */
  public function checkPhpSapi($value): ?string {
    if(PHP_SAPI != $value) {
      return "the sapi is not $value";
    }
    return null;
  }
}
?>