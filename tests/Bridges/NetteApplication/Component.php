<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

use Nette\Application\UI\Control;

final class Component extends Control
{
    public function render(string $one = "", string $two = ""): void
    {
        echo "<div>abc$one$two</div>";
    }
}
