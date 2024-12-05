<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * Dummy engine for annotations reader
 *
 * @author Jakub Konečný
 */
final class DummyEngine implements IAnnotationsReaderEngine
{
    public function hasAnnotation(string $name, string|object $class, ?string $method = null): bool
    {
        return true;
    }

    public function getAnnotation(string $name, string|object $class, ?string $method = null): mixed
    {
        return "abc";
    }
}
