<?php
declare(strict_types=1);

namespace MyTester;

use RuntimeException;

/**
 * Exception thrown when attempting to create a test suite of wrong class
 *
 * @author Jakub Konečný
 */
class InvalidTestSuiteException extends RuntimeException
{
}
