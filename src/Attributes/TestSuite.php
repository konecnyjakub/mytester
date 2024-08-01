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
final class TestSuite extends BaseAttribute
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
