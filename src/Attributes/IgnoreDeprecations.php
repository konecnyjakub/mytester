<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Ignore deprecations attribute
 *
 * Causes deprecations to not be reported for a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class IgnoreDeprecations
{
}
