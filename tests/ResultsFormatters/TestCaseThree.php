<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\DataProvider;
use MyTester\Attributes\Group;
use MyTester\TestCase;

/**
 * @author Jakub Konečný
 */
#[Group("three")]
#[Group("test")]
final class TestCaseThree extends TestCase
{
    public function testOne(): void
    {
        $this->markTestSkipped("abc");
    }

    public function testTwo(): void
    {
        $this->assertTrue(true);
        $this->markTestIncomplete();
    }

    #[DataProvider("dataProvider")]
    public function testDataProvider(int $number, string $text): void
    {
        $this->assertGreaterThan(2, $number);
    }

    /**
     * @return array<int|string, array{0: int, 1: string}>
     */
    public function dataProvider(): array
    {
        return [
            "first" => [1, "abc", ],
            [2, "def", ],
        ];
    }
}
