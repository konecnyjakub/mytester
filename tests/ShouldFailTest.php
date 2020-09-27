<?php

declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Fail;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ShouldFail
 *
 * @author Jakub Konečný
 */
#[TestSuite("ShouldFail")]
final class ShouldFailTest extends TestCase
{
    private function getShouldFailChecker(): ShouldFailChecker
    {
        return $this->shouldFailChecker;
    }

    public function testShouldFail(): void
    {
        $this->assertFalse($this->getShouldFailChecker()->shouldFail(static::class, "shouldFailFalse"));
        $this->assertTrue($this->getShouldFailChecker()->shouldFail(static::class, "shouldFail"));
    }

    private function shouldFailFalse(): void
    {
    }

    #[Fail()]
    private function shouldFail(): void
    {
    }
}
