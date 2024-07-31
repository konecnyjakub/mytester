<?php

declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * Annotations reader
 *
 * @author Jakub Konečný
 * @internal
 */
final class Reader
{
    /** @var IAnnotationsReaderEngine[] */
    private array $engines = [];

    public function registerEngine(IAnnotationsReaderEngine $engine): void
    {
        $this->engines[] = $engine;
    }

    public function hasAnnotation(string $name, string|object $class, string $method = null): bool
    {
        foreach ($this->engines as $engine) {
            if ($engine->hasAnnotation($name, $class, $method)) {
                return true;
            }
        }
        return false;
    }

    public function getAnnotation(string $name, string|object $class, string $method = null): mixed
    {
        foreach ($this->engines as $engine) {
            $value = $engine->getAnnotation($name, $class, $method);
            if ($value !== null) {
                return $value;
            }
        }
        return null;
    }
}
