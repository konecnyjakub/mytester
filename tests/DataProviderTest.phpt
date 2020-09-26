<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class DataProvider
 *
 * @testSuit DataProvider
 * @author Jakub Konečný
 */
final class DataProviderTest extends TestCase {
  private function getDataProvider(): DataProvider {
    static $dataProvider = null;
    if($dataProvider === null) {
      $dataProvider = new DataProvider();
    }
    return $dataProvider;
  }

  public function testGetData(): void {
    $data = $this->getDataProvider()->getData(static::class, "noData");
    $this->assertType("array", $data);
    $this->assertCount(0, $data);

    $data = $this->getDataProvider()->getData(static::class, "noParameters");
    $this->assertType("array", $data);
    $this->assertCount(0, $data);

    $data = $this->getDataProvider()->getData(static::class, "data");
    $this->assertType("array", $data);
    $this->assertCount(2, $data);
  }

  private function noData(): void {
  }

  /**
   * @data(abc, def)
   */
  private function noParameters(): void {
  }

  /**
   * @data(abc, def)
   */
  private function data(string $input): void {
  }
}
?>