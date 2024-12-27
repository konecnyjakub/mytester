<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\ITesterExtension;

final readonly class ExtensionsLoaded
{
    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(public array $extensions = [])
    {
    }
}
