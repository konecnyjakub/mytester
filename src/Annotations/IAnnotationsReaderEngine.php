<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * @author Jakub Konečný
 */
interface IAnnotationsReaderEngine
{
    /**
     * @param class-string|object $class
     */
    public function hasAnnotation(string $name, string|object $class, ?string $method = null): bool;

    /**
     * @param class-string|object $class
     */
    public function getAnnotation(string $name, string|object $class, ?string $method = null): mixed;
}
