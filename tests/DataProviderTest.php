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

        $this->assertThrowsException(function () {
            $this->getDataProvider()->getData($this, "dataProviderNonExisting");
        }, InvalidDataProviderException::class, "Method MyTester\DataProviderTest::nonExisting does not exist.");

        $this->assertThrowsException(function () {
            $this->getDataProvider()->getData($this, "dataProviderPrivate");
        }, InvalidDataProviderException::class, "Method MyTester\DataProviderTest::noData is not public.");

        $this->assertThrowsException(function () {
            $this->getDataProvider()->getData($this, "dataProviderNonArray");
        }, InvalidDataProviderException::class, "Method MyTester\DataProviderTest::dataSourceNonArray has to return an array.");
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

    #[DataProviderAttribute("nonExisting")]
    private function dataProviderNonExisting(string $input): void
    {
    }

    #[DataProviderAttribute("noData")]
    private function dataProviderPrivate(string $input): void
    {
    }

    #[DataProviderAttribute("dataSourceNonArray")]
    private function dataProviderNonArray(string $input): void
    {
    }

    public function dataSource(): array
    {
        return [
            ["abc", ],
            ["def", ],
        ];
    }

    public function dataSourceNonArray(): string
    {
        return "abc";
    }
}
