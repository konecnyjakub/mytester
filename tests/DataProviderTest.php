<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Attributes\Data;
use MyTester\Annotations\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Annotations\Attributes\TestSuit;

/**
 * Test suite for class DataProvider
 *
 * @testSuit DataProvider
 * @author Jakub Konečný
 */
#[TestSuit("DataProvider")]
final class DataProviderTest extends TestCase {
  private function getDataProvider(): DataProvider {
    return $this->dataProvider;
  }

  public function testGetData(): void {
    $data = $this->getDataProvider()->getData($this, "noData");
    $this->assertType("array", $data);
    $this->assertCount(0, $data);

    $data = $this->getDataProvider()->getData($this, "noParameters");
    $this->assertType("array", $data);
    $this->assertCount(0, $data);

    $data = $this->getDataProvider()->getData($this, "data");
    $this->assertType("array", $data);
    $this->assertCount(2, $data);

    $data = $this->getDataProvider()->getData($this, "dataProvider");
    $this->assertType("array", $data);
    $this->assertCount(2, $data);
  }

  private function noData(): void {
  }

  /**
   * @data(abc, def)
   */
  #[Data(["abc", "def"])]
  private function noParameters(): void {
  }

  /**
   * @data(abc, def)
   */
  #[Data(["abc", "def"])]
  private function data(string $input): void {
  }

  /**
   * @dataProvider(dataSource)
   */
  #[DataProviderAttribute("dataSource")]
  private function dataProvider(string $input): void {
  }

  public function dataSource(): array {
    return [
      ["abc", ],
      ["def", ],
    ];
  }
}
?>