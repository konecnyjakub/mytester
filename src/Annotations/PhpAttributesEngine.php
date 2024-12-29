<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Php attributes engine for annotations reader
 *
 * @author Jakub Konečný
 */
final class PhpAttributesEngine implements IAnnotationsReaderEngine
{
    /**
     * @param class-string|object $class
     * @throws ReflectionException
     */
    public function hasAnnotation(string $name, string|object $class, ?string $method = null): bool
    {
        return count($this->getReflection($class, $method)->getAttributes($this->getClassName($name))) > 0;
    }

    /**
     * @param class-string|object $class
     * @throws ReflectionException
     */
    public function getAnnotation(string $name, string|object $class, ?string $method = null): mixed
    {
        $attributes = $this->getReflection($class, $method)->getAttributes($this->getClassName($name));
        if (count($attributes) === 0) {
            return null;
        }
        $attribute = $attributes[0]->newInstance();
        if (property_exists($attribute, "value")) {
            return $attribute->value;
        }
        if (method_exists($attribute, "getValue")) {
            return $attribute->getValue();
        }
        return null;
    }

    private function getClassName(string $baseName): string
    {
        return "MyTester\\Attributes\\" . ucfirst($baseName);
    }

    /**
     * @param class-string|object $class
     * @throws ReflectionException
     */
    private function getReflection(string|object $class, ?string $method = null): ReflectionClass|ReflectionMethod
    {
        if ($method !== null) {
            return new ReflectionMethod(is_object($class) ? get_class($class) : $class, $method);
        }
        return new ReflectionClass(is_object($class) ? get_class($class) : $class);
    }
}
