<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * No assertions attribute
 *
 * Suppresses a warning about no performed assertions in a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class NoAssertions
{
}
