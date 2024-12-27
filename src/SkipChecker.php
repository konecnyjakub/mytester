<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use MyTester\Attributes\RequiresOsFamily;
use MyTester\Attributes\RequiresPhpExtension;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\RequiresSapi;
use Nette\Utils\Arrays;

/**
 * Default skip checker for {@see Tester}
 *
 * @author Jakub Konečný
 */
final class SkipChecker implements ISkipChecker
{
    public const string ANNOTATION_NAME = "skip";

    private array $skipAnnotations = [
        "skip" => Attributes\Skip::class,
        "requiresPhpVersion" => Attributes\RequiresPhpVersion::class,
        "requiresPhpExtension" => Attributes\RequiresPhpExtension::class,
        "requiresSapi" => Attributes\RequiresSapi::class,
        "requiresOsFamily" => Attributes\RequiresOsFamily::class,
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
            $value = $this->annotationsReader->getAnnotation($annotationName, $class, $method);
            if (is_string($value)) {
                /** @var ISkipAttribute $attribute */
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
