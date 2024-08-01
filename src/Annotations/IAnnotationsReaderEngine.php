<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * @author Jakub Konečný
 * @internal
 */
interface IAnnotationsReaderEngine
{
    public function hasAnnotation(string $name, string|object $class, string $method = null): bool;

    public function getAnnotation(string $name, string|object $class, string $method = null): mixed;
}
