<?php
declare(strict_types=1);

namespace MyTester;

use Generator;
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
    public function testGetData(): void
    {
        $dataProvider = new DataProvider($this->annotationsReader);

        /** @var array[] $data */
        $data = $dataProvider->getData($this, "noData");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        /** @var array[] $data */
        $data = $dataProvider->getData($this, "noParameters");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        /** @var array[] $data */
        $data = $dataProvider->getData($this, "dataProvider");
        $this->assertType("array", $data);
        $this->assertCount(2, $data);

        $data = $dataProvider->getData($this, "dataProviderIterable");
        $this->assertType(Generator::class, $data);
        $this->assertCount(2, iterator_to_array($data));

        $this->assertThrowsException(function () use ($dataProvider) {
            $dataProvider->getData($this, "dataProviderNonExisting");
        }, InvalidDataProviderException::class, "Method MyTester\DataProviderTest::nonExisting does not exist.");

        $this->assertThrowsException(function () use ($dataProvider) {
            $dataProvider->getData($this, "dataProviderPrivate");
        }, InvalidDataProviderException::class, "Method MyTester\DataProviderTest::noData is not public.");

        $this->assertThrowsException(
            function () use ($dataProvider) {
                $dataProvider->getData($this, "dataProviderNonArray");
            },
            InvalidDataProviderException::class,
            "Method MyTester\DataProviderTest::dataSourceNonArray has to return an array or an iterable object."
        );
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

    #[DataProviderAttribute("dataSourceIterable")]
    private function dataProviderIterable(int $number): void
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

    public function dataSourceIterable(): iterable
    {
        yield [1, ];
        yield [4, ];
    }
}
