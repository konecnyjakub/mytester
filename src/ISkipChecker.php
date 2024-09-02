<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Skip checker for test methods in {@see TestCase}
 *
 * @author Jakub Konečný
 * @internal
 */
interface ISkipChecker
{
    /**
     * Check whether to skip a test method
     *
     * @return bool|string True/false/reason why it should be skipped
     */
    public function shouldSkip(string $class, string $method): bool|string;
}
