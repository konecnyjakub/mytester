<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Determines whether a test should be skipped
 *
 * @author Jakub Konečný
 */
interface SkipAttribute
{
    /**
     * @return string|null Null or reason why the test should be skipped
     */
    public function getSkipValue(): ?string;
}
