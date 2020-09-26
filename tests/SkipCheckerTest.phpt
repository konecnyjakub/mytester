<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Utils\ArrayHash;

/**
 * Test suite for class SkipChecker
 *
 * @testSuit SkipCheckerTest
 * @author Jakub Konečný
 */
final class SkipCheckerTest extends TestCase {
  private function getSkipChecker(): SkipChecker {
    static $checker = null;
    if($checker === null) {
      $checker = new SkipChecker();
    }
    return $checker;
  }

  public function testCheckPhpVersion(): void {
    $this->assertNull($this->getSkipChecker()->checkPhpVersion(1));
    $this->assertType("string", $this->getSkipChecker()->checkPhpVersion(PHP_INT_MAX));
  }

  public function testCheckLoadedExtension(): void {
    $this->assertNull($this->getSkipChecker()->checkLoadedExtension("ctype"));
    $this->assertType("string", $this->getSkipChecker()->checkLoadedExtension("abc"));
  }

  public function testCheckPhpSapi(): void {
    $this->assertType("string", $this->getSkipChecker()->checkPhpSapi("cgi-fcgi"));
  }

  public function testGetSkipValue(): void {
    $this->assertNull($this->getSkipChecker()->getSkipValue(static::class, "skipNull"));
    $this->assertTrue($this->getSkipChecker()->getSkipValue(static::class, "skip"));
    $this->assertSame(false, $this->getSkipChecker()->getSkipValue(static::class, "skipFalse"));
    $this->assertSame(1.5, $this->getSkipChecker()->getSkipValue(static::class, "skipFloat"));
    $this->assertSame("abc", $this->getSkipChecker()->getSkipValue(static::class, "skipString"));
    $array = $this->getSkipChecker()->getSkipValue(static::class, "skipArray");
    $this->assertType(ArrayHash::class, $array);
    $this->assertCount(1, $array);
  }

  public function testShouldSkip(): void {
    $this->assertFalse($this->getSkipChecker()->shouldSkip(static::class, "skipNull"));
    $this->assertTrue($this->getSkipChecker()->shouldSkip(static::class, "skip"));
    $this->assertFalse($this->getSkipChecker()->shouldSkip(static::class, "skipFalse"));
    $this->assertTrue($this->getSkipChecker()->shouldSkip(static::class, "skipInteger"));
    $this->assertTrue($this->getSkipChecker()->shouldSkip(static::class, "skipFloat"));
    $this->assertTrue($this->getSkipChecker()->shouldSkip(static::class, "skipString"));
    $this->assertTrue($this->getSkipChecker()->shouldSkip(static::class, "skipArray"));
  }

  private function skipNull(): void {
  }

  /**
   * @skip
   */
  private function skip(): void {
  }

  /**
   * @skip(false)
   */
  private function skipFalse(): void {
  }

  /**
   * @skip(1)
   */
  private function skipInteger(): void {
  }

  /**
   * @skip(1.5)
   */
  private function skipFloat(): void {
  }

  /**
   * @skip(abc)
   */
  private function skipString(): void {
  }

  /**
   * @skip(php=666)
   */
  private function skipArray(): void {
  }
}
?>