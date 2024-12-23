<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use Nette\Utils\Arrays;

/**
 * Default skip checker for {@see Tester}
 *
 * @author Jakub Konečný
 */
final class SkipChecker implements ISkipChecker
{
    public const string ANNOTATION_NAME = "skip";

    /** @var callable[] */
    private array $checkers = [];

    public function __construct(private readonly Reader $annotationsReader)
    {
        $this->addDefaultCheckers();
    }

    private function addDefaultCheckers(): void
    {
        $this->addChecker("php", [$this, "checkPhpVersion"]);
        $this->addChecker("extension", [$this, "checkLoadedExtension"]);
        $this->addChecker("sapi", [$this, "checkPhpSapi"]);
        $this->addChecker("osFamily", [$this, "checkOsFamily"]);
    }

    public function addChecker(string $name, callable $callback): void
    {
        $this->checkers[$name] = $callback;
    }

    /**
     * @param class-string $class
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
        $value = $this->getSkipValue($class, $method);
        if ($value === null) {
            return false;
        }
        if ($value === []) {
            return true;
        }
        foreach ($value as $k => $v) {
            $checker = Arrays::get($this->checkers, $k, null);
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

    public function checkPhpVersion(mixed $value): ?string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }
        if (version_compare(PHP_VERSION, (string) $value, "<")) {
            return "PHP version is lesser than $value";
        }
        return null;
    }

    public function checkLoadedExtension(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        if (!extension_loaded($value)) {
            return "extension $value is not loaded";
        }
        return null;
    }

    public function checkPhpSapi(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        if (PHP_SAPI !== $value) {
            return "the sapi is not $value";
        }
        return null;
    }

    /**
     * @see PHP_OS_FAMILY
     */
    public function checkOsFamily(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        if (PHP_OS_FAMILY !== $value) {
            return "os family is not $value";
        }
        return null;
    }
}
