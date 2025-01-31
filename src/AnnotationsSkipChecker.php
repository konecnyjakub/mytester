<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;

/**
 * Default skip checker for {@see Tester}
 *
 * @author Jakub Konečný
 */
final class AnnotationsSkipChecker implements ISkipChecker
{
    /** @var array<string, class-string<ISkipAttribute>>  */
    private array $skipAnnotations = [
        "skip" => Attributes\Skip::class,
        "requiresPhpVersion" => Attributes\RequiresPhpVersion::class,
        "requiresPhpExtension" => Attributes\RequiresPhpExtension::class,
        "requiresSapi" => Attributes\RequiresSapi::class,
        "requiresOsFamily" => Attributes\RequiresOsFamily::class,
        "requiresPackage" => Attributes\RequiresPackage::class,
    ];

    public function __construct(private readonly Reader $annotationsReader)
    {
    }

    /**
     * @param class-string $class
     */
    public function shouldSkip(string $class, string $method): bool|string
    {
        foreach ($this->skipAnnotations as $annotationName => $classname) {
            $values = $this->annotationsReader->getAnnotationMulti($annotationName, $class);
            $skipValue = $this->getSkipValue($values, $classname);
            if ($skipValue !== false) {
                return $skipValue;
            }

            $values = $this->annotationsReader->getAnnotationMulti($annotationName, $class, $method);
            $skipValue = $this->getSkipValue($values, $classname);
            if ($skipValue !== false) {
                return $skipValue;
            }
        }
        return false;
    }

    /**
     * @param mixed[] $values
     * @param class-string<ISkipAttribute> $classname
     */
    private function getSkipValue(array $values, string $classname): bool|string
    {
        foreach ($values as $value) {
            if (is_string($value)) {
                $attribute = new $classname($value);
                $skipValue = $attribute->getSkipValue();
                if (is_string($skipValue)) {
                    if ($skipValue === "") {
                        return true;
                    }
                    return $skipValue;
                }
            }
        }
        return false;
    }
}
