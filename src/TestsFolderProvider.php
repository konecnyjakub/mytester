<?php
declare(strict_types=1);

namespace MyTester;

final readonly class TestsFolderProvider
{
    public function __construct(public string $folder)
    {
    }
}
