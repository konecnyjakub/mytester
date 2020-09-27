<?php

declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class DataProvider
 *
 * @author Jakub Konečný
 */
#[TestSuite("DataProvider")]
final class DataProviderTest extends TestCase
{
    private function getDataProvider(): DataProvider
    {
        return $this->dataProvider;
    }

    public function testGetData(): void
    {
        $data = $this->getDataProvider()->getData($this, "noData");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        $data = $this->getDataProvider()->getData($this, "noParameters");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        $data = $this->getDataProvider()->getData($this, "dataProvider");
        $this->assertType("array", $data);
        $this->assertCount(2, $data);
    }

    private function noData(): void
    {
    }

    #[DataProviderAttribute("dataSource")]
    private function noParameters(): void
    {
    }

    #[DataProviderAttribute("dataSource")]
    private function dataProvider(string $input): void
    {
    }

    public function dataSource(): array
    {
        return [
            ["abc", ],
            ["def", ],
        ];
    }
}
