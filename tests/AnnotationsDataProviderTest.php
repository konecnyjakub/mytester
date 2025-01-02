<?php
declare(strict_types=1);

namespace MyTester;

use Generator;
use MyTester\Attributes\Data;
use MyTester\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Attributes\DataProviderExternal;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class AnnotationsDataProvider
 *
 * @author Jakub Konečný
 */
#[TestSuite("AnnotationsDataProvider")]
final class AnnotationsDataProviderTest extends TestCase
{
    public function testGetData(): void
    {
        $dataProvider = new AnnotationsDataProvider($this->annotationsReader);

        /** @var array{} $data */
        $data = $dataProvider->getData($this, "noData");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        /** @var array{} $data */
        $data = $dataProvider->getData($this, "noParameters");
        $this->assertType("array", $data);
        $this->assertCount(0, $data);

        /** @var array<int, string>[] $data */
        $data = $dataProvider->getData($this, "dataProvider");
        $this->assertType("array", $data);
        $this->assertCount(2, $data);

        $data = $dataProvider->getData($this, "dataProviderIterable");
        $this->assertType(Generator::class, $data);
        $this->assertCount(2, iterator_to_array($data));

        /** @var array<int|string, array{0: string, 1: int}> $data */
        $data = $dataProvider->getData($this, "dataProviderExternal");
        $this->assertType("array", $data);
        $this->assertCount(2, $data);

        /** @var string[][] $data */
        $data = $dataProvider->getData($this, "dataProviderSimple");
        $this->assertType("array", $data);
        $this->assertCount(2, $data);

        $this->assertThrowsException(function () use ($dataProvider) {
            $dataProvider->getData($this, "dataProviderNonExisting");
        }, InvalidDataProviderException::class, "Method " . self::class . "::nonExisting does not exist.");

        $this->assertThrowsException(function () use ($dataProvider) {
            $dataProvider->getData($this, "dataProviderPrivate");
        }, InvalidDataProviderException::class, "Method " . self::class . "::noData is not public.");

        $this->assertThrowsException(
            function () use ($dataProvider) {
                $dataProvider->getData($this, "dataProviderNonArray");
            },
            InvalidDataProviderException::class,
            "Method " . self::class . "::dataSourceNonArray has to return an array or an iterable object."
        );

        $this->assertThrowsException(
            function () use ($dataProvider) {
                $dataProvider->getData($this, "dataProviderExternalNonStatic");
            },
            InvalidDataProviderException::class,
            "Method " . ExternalDataProvider::class . "::dataProviderNonStatic is not static."
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

    #[DataProviderExternal(ExternalDataProvider::class, "dataProviderArray")]
    private function dataProviderExternal(string $input): void
    {
    }

    #[DataProviderExternal(ExternalDataProvider::class, "dataProviderNonStatic")]
    private function dataProviderExternalNonStatic(string $input): void
    {
    }

    #[Data(["abc", "def", ])]
    #[Data(["ghi", "jkl", ])]
    private function dataProviderSimple(string $text1, string $text2): void
    {
    }

    /**
     * @return array<int, string[]>
     */
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

    /**
     * @return iterable<int, int[]>
     */
    public function dataSourceIterable(): iterable
    {
        yield [1, ];
        yield [4, ];
    }
}
