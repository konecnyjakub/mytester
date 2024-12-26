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

    /**
     * @var callable[]
     * @deprecated
     */
    private array $checkers = [];

    private array $skipAnnotations = [
        "requiresPhpVersion" => Attributes\RequiresPhpVersion::class,
        "requiresPhpExtension" => Attributes\RequiresPhpExtension::class,
        "requiresSapi" => Attributes\RequiresSapi::class,
        "requiresOsFamily" => Attributes\RequiresOsFamily::class,
    ];

    public function __construct(private readonly Reader $annotationsReader)
    {
        $this->addChecker("php", [$this, "checkPhpVersion"]); // @phpstan-ignore method.deprecated
        $this->addChecker("extension", [$this, "checkLoadedExtension"]); // @phpstan-ignore method.deprecated
        $this->addChecker("sapi", [$this, "checkPhpSapi"]); // @phpstan-ignore method.deprecated
        $this->addChecker("osFamily", [$this, "checkOsFamily"]); // @phpstan-ignore method.deprecated
    }

    /**
     * @deprecated Use new attributes instead
     */
    public function addChecker(string $name, callable $callback): void
    {
        $this->checkers[$name] = $callback;
    }

    /**
     * @param class-string $class
     * @deprecated
     */
    public function getSkipValue(string $class, string $method): ?array
    {
        /** @var array|null $value */
        $value = $this->annotationsReader->getAnnotation(self::ANNOTATION_NAME, $class, $method);
        return $value;
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
                    return $skipValue;
                }
            }
        }

        $value = $this->getSkipValue($class, $method); // @phpstan-ignore method.deprecated
        if ($value === null) {
            return false;
        }
        if ($value === []) {
            return true;
        }
        foreach ($value as $k => $v) {
            $checker = Arrays::get($this->checkers, $k, null); // @phpstan-ignore property.deprecated
            if ($checker === null) {
                return false;
            }
            $value = $checker($v);
            if (is_string($value)) {
                return $value;
            }
        }
        return false;
    }

    /**
     * @deprecated Use attribute {@see RequiresPhpVersion} instead
     */
    public function checkPhpVersion(mixed $value): ?string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }
        trigger_error("Using an array to skip a test is deprecated, use an attribute instead", E_USER_DEPRECATED);
        return (new Attributes\RequiresPhpVersion((string) $value))->getSkipValue();
    }

    /**
     * @deprecated Use attribute {@see RequiresPhpExtension} instead
     */
    public function checkLoadedExtension(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        trigger_error("Using an array to skip a test is deprecated, use an attribute instead", E_USER_DEPRECATED);
        return (new Attributes\RequiresPhpExtension($value))->getSkipValue();
    }

    /**
     * @deprecated Use attribute {@see RequiresSapi} instead
     */
    public function checkPhpSapi(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        trigger_error("Using an array to skip a test is deprecated, use an attribute instead", E_USER_DEPRECATED);
        return (new Attributes\RequiresSapi($value))->getSkipValue();
    }

    /**
     * @see PHP_OS_FAMILY
     * @deprecated Use attribute {@see RequiresOsFamily} instead
     */
    public function checkOsFamily(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        trigger_error("Using an array to skip a test is deprecated, use an attribute instead", E_USER_DEPRECATED);
        return (new Attributes\RequiresOsFamily($value))->getSkipValue();
    }
}
