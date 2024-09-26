<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * TestSuite attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class TestSuite
{
    public function __construct(public string $value)
    {
    }
}
