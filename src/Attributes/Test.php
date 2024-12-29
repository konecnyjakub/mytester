<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Test attribute
 * Provides custom name for a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Test
{
    public function __construct(public string $value)
    {
    }
}
