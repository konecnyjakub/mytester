<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Skip attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Skip extends BaseAttribute
{
    public function __construct(public mixed $value = true)
    {
    }
}
