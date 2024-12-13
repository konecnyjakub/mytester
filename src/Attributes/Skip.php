<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Skip attribute
 * Defines conditions when a test should be skipped
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Skip
{
    public function __construct(public array $value = [])
    {
    }
}
