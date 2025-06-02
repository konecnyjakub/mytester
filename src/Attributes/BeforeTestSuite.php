<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Before test case attribute
 * Marks a method to run before the test suite
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class BeforeTestSuite
{
}
