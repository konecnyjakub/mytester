<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

use MyTester\Attributes\Group;
use Nette\Application\UI\Control;

#[Group("nette")]
final class Component extends Control
{
    public function render(string $one = "", string $two = ""): void
    {
        echo "<div>abc$one$two</div>";
    }
}
