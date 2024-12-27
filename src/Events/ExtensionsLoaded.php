<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\ITesterExtension;

/**
 * Triggers when extensions for automated tests runner are loaded
 *
 * @author Jakub Konečný
 */
final readonly class ExtensionsLoaded
{
    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(public array $extensions = [])
    {
    }
}
