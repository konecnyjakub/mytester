<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Provides path to folder with tests
 *
 * @author Jakub Konečný
 */
final readonly class TestsFolderProvider
{
    public function __construct(public string $folder)
    {
    }
}
