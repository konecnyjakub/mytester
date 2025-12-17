<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * Engine for {@see Reader}
 *
 * @author Jakub Konečný
 */
interface AnnotationsReaderEngine
{
    /**
     * @param class-string|object $class
     */
    public function hasAnnotation(string $name, string|object $class, ?string $method = null): bool;

    /**
     * @param class-string|object $class
     */
    public function getAnnotation(string $name, string|object $class, ?string $method = null): mixed;

    /**
     * Get values from annotation that can be used multiple times
     * Each value in the array is from one annotation
     *
     * @param class-string|object $class
     * @return mixed[]
     */
    public function getAnnotationMulti(string $name, string|object $class, ?string $method = null): array;
}
