<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * After test attribute
 * Marks a method to run after each test in the test case
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class AfterTest
{
}
