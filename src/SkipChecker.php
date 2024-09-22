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
    use \Nette\SmartObject;

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
    }

    public function addChecker(string $name, callable $callback): void
    {
        $this->checkers[$name] = $callback;
    }

    public function getSkipValue(string $class, string $method): mixed
    {
        return $this->annotationsReader->getAnnotation(static::ANNOTATION_NAME, $class, $method);
    }

    public function shouldSkip(string $class, string $method): bool|string
    {
        $value = $this->getSkipValue($class, $method);
        if (is_scalar($value)) {
            return (bool) $value;
        } elseif (is_iterable($value)) {
            foreach ($value as $k => $v) {
                $checker = Arrays::get($this->checkers, $k, null);
                if ($checker === null) {
                    return false;
                }
                $value = $checker($v);
                return $value ?? false;
            }
        }
        return false;
    }

    public function checkPhpVersion(mixed $value): ?string
    {
        if (version_compare(PHP_VERSION, (string) $value, "<")) {
            return "PHP version is lesser than $value";
        }
        return null;
    }

    public function checkLoadedExtension(mixed $value): ?string
    {
        if (!extension_loaded($value)) {
            return "extension $value is not loaded";
        }
        return null;
    }

    public function checkPhpSapi(mixed $value): ?string
    {
        if (PHP_SAPI != $value) {
            return "the sapi is not $value";
        }
        return null;
    }
}
