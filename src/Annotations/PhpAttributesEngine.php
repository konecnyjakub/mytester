<?php

declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Annotations\Attributes\BaseAttribute;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Reflector;

/**
 * Php attributes engine for annotations reader
 *
 * @author Jakub Konečný
 * @internal
 */
final class PhpAttributesEngine implements \MyTester\IAnnotationsReaderEngine
{
    public function hasAnnotation(string $name, $class, string $method = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        return count($this->getReflection($class, $method)->getAttributes($this->getClassName($name))) > 0;
    }

    public function getAnnotation(string $name, $class, string $method = null)
    {
        if (!$this->isAvailable()) {
            return null;
        }
        $attributes = $this->getReflection($class, $method)->getAttributes($this->getClassName($name));
        if (count($attributes) === 0) {
            return null;
        }
        /** @var BaseAttribute $attribute */
        $attribute = $attributes[0]->newInstance();
        return $attribute->value;
    }

    private function isAvailable(): bool
    {
        return version_compare(PHP_VERSION, "7.5.0", ">");
    }

    private function getClassName(string $baseName): string
    {
        return "MyTester\\Annotations\Attributes\\" . Strings::firstUpper($baseName);
    }

    /**
     * @param string|object $class
     * @return ReflectionClass|ReflectionMethod
     * @throws ReflectionException
     */
    private function getReflection($class, string $method = null): Reflector
    {
        if ($method !== null) {
            $reflection = new ReflectionMethod(is_object($class) ? get_class($class) : $class, $method);
        } else {
            $reflection = new ReflectionClass(is_object($class) ? get_class($class) : $class);
        }
        return $reflection;
    }
}
