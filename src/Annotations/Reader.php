<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * Annotations reader
 *
 * @author Jakub Konečný
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
     * @param class-string|object $class
     */
    public function hasAnnotation(string $name, string|object $class, ?string $method = null): bool
    {
        foreach ($this->engines as $engine) {
            if ($engine->hasAnnotation($name, $class, $method)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param class-string|object $class
     */
    public function getAnnotation(string $name, string|object $class, ?string $method = null): mixed
    {
        foreach ($this->engines as $engine) {
            $value = $engine->getAnnotation($name, $class, $method);
            if ($value !== null) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get values from annotation that can be used multiple times
     * Each value in the array is from one annotation
     *
     * @param class-string|object $class
     * @return mixed[]
     */
    public function getAnnotationMulti(string $name, string|object $class, ?string $method = null): array
    {
        foreach ($this->engines as $engine) {
            $value = $engine->getAnnotationMulti($name, $class, $method);
            if (count($value) > 0) {
                return $value;
            }
        }
        return [];
    }
}
