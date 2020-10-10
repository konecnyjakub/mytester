<?php

declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\IAnnotationsReaderEngine;

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

    /**
     * @param string|object $class
     */
    public function hasAnnotation(string $name, $class, string $method = null): bool
    {
        foreach ($this->engines as $engine) {
            if ($engine->hasAnnotation($name, $class, $method)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string|object $class
     * @return mixed
     */
    public function getAnnotation(string $name, $class, string $method = null)
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
